<?php
/**
 * NebulaPOS - Wompi payment gateway
 *
 * No simula aprobaciones. Si Wompi no confirma la transacción, la operación
 * se devuelve como error/pending y se persiste su estado para trazabilidad.
 */
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/curl_helper.php';

$baseUrl = rtrim((string)(getenv('WOMPI_BASE_URL') ?: 'https://sandbox.wompi.co/v1'), '/');
$publicKey = (string)(getenv('WOMPI_PUBLIC_KEY') ?: '');
$privateKey = (string)(getenv('WOMPI_PRIVATE_KEY') ?: '');

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'error','message'=>'Método HTTP no permitido.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) $data = $_POST;
$action = $data['action'] ?? 'pay';

if ($action === 'get_acceptance') {
    getAcceptanceToken($baseUrl, $publicKey);
    exit;
}
if ($action !== 'pay') {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Acción no válida.']);
    exit;
}
processPayment($pdo, $baseUrl, $publicKey, $privateKey, $data);

function getAcceptanceToken(string $baseUrl, string $publicKey): void
{
    if ($publicKey === '') {
        http_response_code(503);
        echo json_encode(['status'=>'error','message'=>'Wompi no está configurado en el servidor.']);
        return;
    }
    $response = CurlHelper::get($baseUrl . '/merchants/' . rawurlencode($publicKey));
    if (($response['status'] ?? '') === 'success' && isset($response['data']['data']['presigned_acceptance']['token'])) {
        echo json_encode([
            'status'=>'success',
            'acceptance_token'=>$response['data']['data']['presigned_acceptance']['token'],
            'permalink'=>$response['data']['data']['presigned_acceptance']['permalink'] ?? null
        ]);
        return;
    }
    http_response_code(502);
    echo json_encode(['status'=>'error','message'=>'No fue posible obtener el token de aceptación de Wompi.']);
}

function processPayment(PDO $pdo,string $baseUrl,string $publicKey,string $privateKey,array $data): void
{
    $amountInCents = (int)($data['amount_in_cents'] ?? 0);
    $currency = strtoupper(trim((string)($data['currency'] ?? 'COP')));
    $customerEmail = trim((string)($data['customer_email'] ?? ''));
    $saleId = isset($data['sale_id']) ? (string)$data['sale_id'] : null;
    $companyId = isset($_SESSION['empresa_id']) ? (int)$_SESSION['empresa_id'] : null;

    if ($amountInCents <= 0 || !filter_var($customerEmail,FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status'=>'error','message'=>'Monto o correo del cliente inválido.']);
        return;
    }
    if ($publicKey === '' || $privateKey === '') {
        http_response_code(503);
        echo json_encode(['status'=>'error','message'=>'Wompi no está configurado en el servidor.']);
        return;
    }

    $reference = 'NEBULA-' . gmdate('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(4)));
    $cardNumber = preg_replace('/\D+/','',(string)($data['card_number'] ?? ''));
    $expMonth = str_pad(trim((string)($data['exp_month'] ?? '')),2,'0',STR_PAD_LEFT);
    $expYear = trim((string)($data['exp_year'] ?? ''));
    $cvc = trim((string)($data['cvc'] ?? ''));
    $cardHolder = trim((string)($data['card_holder'] ?? ''));
    $installments = max(1,(int)($data['installments'] ?? 1));

    if ($cardNumber === '' || $expMonth === '00' || $expYear === '' || $cvc === '' || $cardHolder === '') {
        http_response_code(400);
        echo json_encode(['status'=>'error','message'=>'Información de tarjeta incompleta.']);
        return;
    }

    $tokenResult = CurlHelper::post(
        $baseUrl . '/tokens/cards',
        ['number'=>$cardNumber,'cvc'=>$cvc,'exp_month'=>$expMonth,'exp_year'=>$expYear,'card_holder'=>$cardHolder],
        ['Authorization: Bearer '.$publicKey,'Content-Type: application/json']
    );

    if (($tokenResult['status'] ?? '') !== 'success' || empty($tokenResult['data']['data']['id'])) {
        http_response_code(502);
        echo json_encode(['status'=>'error','message'=>'Wompi no pudo tokenizar la tarjeta.']);
        return;
    }
    $cardToken = $tokenResult['data']['data']['id'];

    $acceptanceToken = (string)($data['acceptance_token'] ?? '');
    if ($acceptanceToken === '') {
        $acceptance = CurlHelper::get($baseUrl . '/merchants/' . rawurlencode($publicKey));
        $acceptanceToken = (string)($acceptance['data']['data']['presigned_acceptance']['token'] ?? '');
    }
    if ($acceptanceToken === '') {
        http_response_code(502);
        echo json_encode(['status'=>'error','message'=>'No se pudo obtener el token de aceptación de Wompi.']);
        return;
    }

    $txResult = CurlHelper::post(
        $baseUrl . '/transactions',
        [
            'amount_in_cents'=>$amountInCents,
            'currency'=>$currency,
            'customer_email'=>$customerEmail,
            'payment_method'=>['type'=>'CARD','token'=>$cardToken,'installments'=>$installments],
            'reference'=>$reference,
            'acceptance_token'=>$acceptanceToken
        ],
        ['Authorization: Bearer '.$privateKey,'Content-Type: application/json']
    );

    $remote = $txResult['data']['data'] ?? null;
    $wompiTxId = is_array($remote) ? (string)($remote['id'] ?? '') : '';
    $txStatus = is_array($remote) ? strtoupper((string)($remote['status'] ?? 'ERROR')) : 'ERROR';

    $stmt = $pdo->prepare('INSERT INTO transactions (transaction_id,reference,amount_in_cents,currency,customer_email,status,payment_method_type,sale_id,empresa_id,wompi_response) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $wompiTxId !== '' ? $wompiTxId : null,
        $reference,$amountInCents,$currency,$customerEmail,$txStatus,'CARD',$saleId,$companyId,
        json_encode($txResult['data'] ?? $txResult,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
    ]);

    if ($wompiTxId === '') {
        http_response_code(502);
        echo json_encode(['status'=>'error','message'=>'Wompi no confirmó la transacción.','reference'=>$reference]);
        return;
    }

    $successStatuses = ['APPROVED'];
    if (!in_array($txStatus,$successStatuses,true)) {
        http_response_code(402);
        echo json_encode(['status'=>'error','message'=>'La transacción no fue aprobada por Wompi.','transaction'=>['id'=>$wompiTxId,'reference'=>$reference,'amount'=>$amountInCents/100,'currency'=>$currency,'status'=>$txStatus]]);
        return;
    }

    if ($saleId !== null) {
        $pay = $pdo->prepare('INSERT INTO payments (sale_id,method,amount,status,reference) VALUES (?,?,?,?,?)');
        $pay->execute([$saleId,'card',$amountInCents/100,'approved',$reference]);
    }

    echo json_encode(['status'=>'success','message'=>'Pago aprobado por Wompi.','transaction'=>['id'=>$wompiTxId,'reference'=>$reference,'amount'=>$amountInCents/100,'currency'=>$currency,'status'=>$txStatus,'customer_email'=>$customerEmail]]);
}
