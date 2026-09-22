<?php


// V3.0 TOKEN MANAGER SIMPLIFICADO

// Verificar si la función ya existe para evitar redeclaración
if (!function_exists('obtener_token_con_curl')) {
    function obtener_token_con_curl() {
        $config = require __DIR__ . '/utils/config.php';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $config["API_AUTH_TEST_URL"],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'user' => $config["NIT"],
                'pwd' => $config["AUTH_KEY"]
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: ANDROMEDA-FACTURACION/1.0'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Error cURL: $error");
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Error HTTP $httpCode");
        }
        
        $json = json_decode($response, true);
        if (!isset($json['body']['token'])) {
            throw new Exception("Token no recibido: " . ($json['message'] ?? 'Error desconocido'));
        }
        
        // Guardar token
        $tokenDir = __DIR__ . '/../../storage/temp/';
        if (!is_dir($tokenDir)) {
            mkdir($tokenDir, 0777, true);
        }

        $tokenData = [
            'token' => $json['body']['token'],
            'expires' => time() + 86400
        ];
        
        $tokenPath = $tokenDir . 'token.json';
        
        file_put_contents($tokenPath, json_encode($tokenData));
        
        return $json['body']['token'];
    }
}

if (!function_exists('obtener_token_valido')) {
    function obtener_token_valido() {
        $tokenPath = __DIR__ . '/../../storage/temp/token.json';
        
        if (file_exists($tokenPath)) {
            $data = json_decode(file_get_contents($tokenPath), true);
            if (isset($data['token'], $data['expires']) && $data['expires'] > time()) {
                return $data['token'];
            }
        }
        
        return obtener_token_con_curl();
    }
}