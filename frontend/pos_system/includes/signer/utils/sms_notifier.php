<?php
function enviar_sms_notificacion(string $telefono, string $mensaje): bool
{
    $config = require __DIR__ . '/config.php';
    
    // Formato internacional sin "+"
    $telefono = preg_replace('/[^0-9]/', '', $telefono);

    $apiKey = $config['CALLMEBOT_API_KEY'] ?? '';
    
    if (empty($apiKey)) return false;

    $url = "https://api.callmebot.com/whatsapp.php?"
         . http_build_query([
             'phone'   => $telefono,
             'text'    => $mensaje,
             'apikey' => $apiKey
         ]);

    $response = @file_get_contents($url);

    return $response !== false;
}
