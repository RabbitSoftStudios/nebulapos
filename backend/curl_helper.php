<?php
/**
 * NebulaPOS - Servicio Helper para Peticiones HTTP cURL
 */

class CurlHelper {
    /**
     * Realiza una petición GET mediante cURL
     */
    public static function get($url, $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 'error', 'message' => $error, 'http_code' => $httpCode];
        }

        return [
            'status' => 'success',
            'http_code' => $httpCode,
            'data' => json_decode($response, true) ?: $response
        ];
    }

    /**
     * Realiza una petición POST mediante cURL
     */
    public static function post($url, $payload, $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if (is_array($payload) || is_object($payload)) {
            $jsonData = json_encode($payload);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            $headers[] = 'Content-Type: application/json';
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 'error', 'message' => $error, 'http_code' => $httpCode];
        }

        return [
            'status' => 'success',
            'http_code' => $httpCode,
            'data' => json_decode($response, true) ?: $response
        ];
    }
}
