<?php
/** NebulaPOS - Wompi payment gateway */
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/curl_helper.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$baseUrl=rtrim((string)(getenv('WOMPI_BASE_URL')?:'https://sandbox.wompi.co/v1'),'/');
$publicKey=(string)(getenv('WOMPI_PUBLIC_KEY')?:'');
$privateKey=(string)(getenv('WOMPI_PRIVATE_KEY')?:'');
$pdo=getDBConnection();

if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);echo json_encode(['status'=>'error','message'=>'Método HTTP no permitido.']);exit;}
$rawInput=file_get_contents('php://input');$data=json_decode($rawInput,true);if(!is_array($data))$data=$_POST;
$action=$data['action']??'pay';
if($action==='get_acceptance'){getAcceptanceToken($baseUrl,$publicKey);exit;}
if($action!=='pay'){http_response_code(400);echo json_encode(['status'=>'error','message'=>'Acción no válida.']);exit;}
processPayment($pdo,$baseUrl,$publicKey,$privateKey,$data);

function getAcceptanceToken(string $baseUrl,string $publicKey):void{
    if($publicKey===''){http_response_code(503);echo json_encode(['status'=>'error','message'=>'Wompi no está configurado en el servidor.']);return;}
    $r=CurlHelper::get($baseUrl.'/merchants/'.rawurlencode($publicKey));
    if(($r['status']??'')==='success'&&isset($r['data']['data']['presigned_acceptance']['token'])){echo json_encode(['status'=>'success','acceptance_token'=>$r['data']['data']['presigned_acceptance']['token'],'permalink'=>$r['data']['data']['presigned_acceptance']['permalink']??null]);return;}
    http_response_code(502);echo json_encode(['status'=>'error','message'=>'No fue posible obtener el token de aceptación de Wompi.']);
}

function processPayment(PDO $pdo,string $baseUrl,string $publicKey,string $privateKey,array $data):void{
    $amount=(int)($data['amount_in_cents']??0);$currency=strtoupper(trim((string)($data['currency']??'COP')));$email=trim((string)($data['customer_email']??''));$saleId=isset($data['sale_id'])?(string)$data['sale_id']:null;$companyId=isset($_SESSION['empresa_id'])?(int)$_SESSION['empresa_id']:null;
    if($amount<=0||!filter_var($email,FILTER_VALIDATE_EMAIL)){http_response_code(400);echo json_encode(['status'=>'error','message'=>'Monto o correo del cliente inválido.']);return;}
    if($publicKey===''||$privateKey===''){http_response_code(503);echo json_encode(['status'=>'error','message'=>'Wompi no está configurado en el servidor.']);return;}
    $reference='NEBULA-'.gmdate('YmdHis').'-'.strtoupper(bin2hex(random_bytes(4)));
    $number=preg_replace('/\D+/','',(string)($data['card_number']??''));$month=str_pad(trim((string)($data['exp_month']??'')),2,'0',STR_PAD_LEFT);$year=trim((string)($data['exp_year']??''));$cvc=trim((string)($data['cvc']??''));$holder=trim((string)($data['card_holder']??''));$installments=max(1,(int)($data['installments']??1));
    if($number===''||$month==='00'||$year===''||$cvc===''||$holder===''){http_response_code(400);echo json_encode(['status'=>'error','message'=>'Información de tarjeta incompleta.']);return;}
    $token=CurlHelper::post($baseUrl.'/tokens/cards',['number'=>$number,'cvc'=>$cvc,'exp_month'=>$month,'exp_year'=>$year,'card_holder'=>$holder],['Authorization: Bearer '.$publicKey,'Content-Type: application/json']);
    if(($token['status']??'')!=='success'||empty($token['data']['data']['id'])){http_response_code(502);echo json_encode(['status'=>'error','message'=>'Wompi no pudo tokenizar la tarjeta.']);return;}
    $acceptance=(string)($data['acceptance_token']??'');
    if($acceptance===''){$a=CurlHelper::get($baseUrl.'/merchants/'.rawurlencode($publicKey));$acceptance=(string)($a['data']['data']['presigned_acceptance']['token']??'');}
    if($acceptance===''){http_response_code(502);echo json_encode(['status'=>'error','message'=>'No se pudo obtener el token de aceptación de Wompi.']);return;}
    $tx=CurlHelper::post($baseUrl.'/transactions',['amount_in_cents'=>$amount,'currency'=>$currency,'customer_email'=>$email,'payment_method'=>['type'=>'CARD','token'=>$token['data']['data']['id'],'installments'=>$installments],'reference'=>$reference,'acceptance_token'=>$acceptance],['Authorization: Bearer '.$privateKey,'Content-Type: application/json']);
    $remote=$tx['data']['data']??null;$txId=is_array($remote)?(string)($remote['id']??''):'';$status=is_array($remote)?strtoupper((string)($remote['status']??'ERROR')):'ERROR';
    $stmt=$pdo->prepare('INSERT INTO transactions (transaction_id,reference,amount_in_cents,currency,customer_email,status,payment_method_type,sale_id,empresa_id,wompi_response) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$txId!==''?$txId:null,$reference,$amount,$currency,$email,$status,'CARD',$saleId,$companyId,json_encode($tx['data']??$tx,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);
    if($txId===''||$status!=='APPROVED'){http_response_code(402);echo json_encode(['status'=>'error','message'=>'La transacción no fue aprobada por Wompi.','transaction'=>['id'=>$txId,'reference'=>$reference,'amount'=>$amount/100,'currency'=>$currency,'status'=>$status]]);return;}
    if($saleId!==null){$pay=$pdo->prepare('INSERT INTO payments (sale_id,method,amount,status,reference) VALUES (?,?,?,?,?)');$pay->execute([$saleId,'card',$amount/100,'approved',$reference]);}
    echo json_encode(['status'=>'success','message'=>'Pago aprobado por Wompi.','transaction'=>['id'=>$txId,'reference'=>$reference,'amount'=>$amount/100,'currency'=>$currency,'status'=>$status,'customer_email'=>$email]]);
}
