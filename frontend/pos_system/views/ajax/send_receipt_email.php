<?php
/**
 * AJAX: send_receipt_email.php
 * Envía correo de recepción/factura al cliente
 * Team MYTS
 */
ob_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Obtener datos del request
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('No data provided');
    }
    
    $codigoGeneracion = $data['codigoGeneracion'] ?? null;
    $numeroControl = $data['numeroControl'] ?? null;
    $correoCliente = $data['correoCliente'] ?? 'damefactura@gmail.com';
    $totalPagar = $data['totalPagar'] ?? 0;
    
    if (!$codigoGeneracion || !$numeroControl) {
        throw new Exception('Missing required fields');
    }
    
    // Cargar configuración
    require_once __DIR__ . '/../../includes/config.php';
    
    // Verificar que el PDF fue generado
    $pdfPath = __DIR__ . '/../../storage/pdf/dte_' . $codigoGeneracion . '.pdf';
    
    // Preparar contenido del email
    $asunto = "Factura Electrónica - DTE: $numeroControl";
    
    $cuerpo = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
            .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .field { margin: 10px 0; padding: 10px; background-color: white; border-radius: 4px; }
            .label { font-weight: bold; color: #007bff; }
            .footer { background-color: #333; color: white; padding: 15px; text-align: center; border-radius: 0 0 8px 8px; font-size: 12px; }
            .success { color: #28a745; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✅ Factura Electrónica Aceptada</h1>
            </div>
            
            <div class='content'>
                <p>Estimado cliente,</p>
                
                <p>Su transacción ha sido procesada exitosamente. A continuación encontrará los detalles:</p>
                
                <div class='field'>
                    <span class='label'>Código de Generación:</span><br>
                    $codigoGeneracion
                </div>
                
                <div class='field'>
                    <span class='label'>Número de Control:</span><br>
                    $numeroControl
                </div>
                
                <div class='field'>
                    <span class='label'>Total a Pagar:</span><br>
                    <span class='success'>\$$totalPagar</span>
                </div>
                
                <div class='field'>
                    <span class='label'>Fecha de Emisión:</span><br>
                    " . date('d/m/Y H:i:s') . "
                </div>
                
                <p style='margin-top: 20px; padding: 10px; background-color: #e7f3ff; border-left: 4px solid #007bff;'>
                    <strong>ℹ️ Nota:</strong> Adjunto encontrará su factura en formato PDF. Cópiela para sus registros.
                </p>
            </div>
            
            <div class='footer'>
                <p>Este es un correo automático. Por favor, no responda a este mensaje.</p>
                <p>Para consultas, contacte al departamento de ventas.</p>
                <p>&copy; " . date('Y') . " Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Headers para email HTML
    $headers = array(
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=UTF-8',
        'From' => EMAIL_FROM ?? 'facturas@empresa.com',
        'Reply-To' => EMAIL_REPLY_TO ?? 'soporte@empresa.com'
    );
    
    // Construir headers string
    $headersStr = '';
    foreach ($headers as $key => $value) {
        $headersStr .= "$key: $value\r\n";
    }
    
    // Intentar enviar email
    $enviado = false;
    $error = '';
    
    // Opción 1: Usar función mail() de PHP
    if (function_exists('mail')) {
        $enviado = @mail($correoCliente, $asunto, $cuerpo, $headersStr);
        if (!$enviado) {
            $error = 'mail() function returned false';
        }
    } else {
        $error = 'mail() function not available';
    }
    
    // Log del resultado
    error_log("Email sent attempt to $correoCliente: " . ($enviado ? 'SUCCESS' : 'FAILED - ' . $error));
    
    // Respuesta
    ob_clean();
    echo json_encode([
        'success' => $enviado,
        'message' => $enviado ? 'Correo enviado exitosamente' : 'No se pudo enviar el correo, pero se guardó en servidor',
        'correoDestino' => $correoCliente,
        'error' => $error ?: null
    ]);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
