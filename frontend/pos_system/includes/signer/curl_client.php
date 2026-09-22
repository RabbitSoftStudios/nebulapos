<?php

class CurlClient
{
    /**
     * Envía una petición POST con JSON
     */
    public static function postJson(string $url, array $data, array $headers = []): array
    {
        $ch = curl_init($url);
        
        // Configuración por defecto
        $defaultHeaders = [
            'Content-Type: application/JSON',
            'Accept: application/json'
        ];

        // Fusionar headers (los del argumento sobrescriben si es necesario, aunque aquí es append)
        $finalHeaders = array_merge($defaultHeaders, $headers);
        // Eliminar duplicados de headers (no perfecto pero ayuda)
        $finalHeaders = array_unique($finalHeaders);

        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $finalHeaders);
        
        // Timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        // SSL (Ajustar según entorno, aquí flexible para compatibilidad)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'error' => "Curl Error: $error",
                'code' => $httpCode
            ];
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => 'Invalid JSON response',
                'raw_response' => $response,
                'code' => $httpCode
            ];
        }

        return [
            'success' => true,
            'data' => $decoded,
            'code' => $httpCode
        ];
    }
}
