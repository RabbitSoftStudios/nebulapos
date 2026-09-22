<?php
/**
 * Configuración de Wompi
 * ======================
 * Centraliza la carga y validación de credenciales
 */

// Cargar variables de entorno si no están definidas
if (!function_exists('loadEnvVariables')) {
    function loadEnvVariables() {
        $envFile = __DIR__ . '/../.env';
        
        if (!file_exists($envFile)) {
            throw new Exception('Archivo .env no encontrado en: ' . $envFile);
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorar comentarios
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parsear linea KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, ' "\'');

                // Definir constante si no existe
                if (!defined($key)) {
                    define($key, $value);
                }
            }
        }
    }
}

// Cargar variables
loadEnvVariables();

// Validar credenciales de Wompi
if (!defined('WOMPI_CLIENT_ID') || empty(WOMPI_CLIENT_ID)) {
    error_log('ERROR: WOMPI_CLIENT_ID no está configurada en .env');
}

if (!defined('WOMPI_CLIENT_SECRET') || empty(WOMPI_CLIENT_SECRET)) {
    error_log('ERROR: WOMPI_CLIENT_SECRET no está configurada en .env');
}

// Configuración de Wompi
return [
    'enabled' => defined('WOMPI_CLIENT_ID') && !empty(WOMPI_CLIENT_ID),
    'clientId' => defined('WOMPI_CLIENT_ID') ? WOMPI_CLIENT_ID : null,
    'clientSecret' => defined('WOMPI_CLIENT_SECRET') ? WOMPI_CLIENT_SECRET : null,
    'api' => [
        'tokenUrl' => 'https://id.wompi.sv/connect/token',
        'apiUrl' => 'https://api.wompi.sv',
        'paymentEndpoint' => 'https://api.wompi.sv/EnlacePago',
        'timeout' => 15
    ],
    'payment' => [
        'environment' => 'test', // 'test' o 'production'
        'currency' => 'USD',
        'locale' => 'es_SV'
    ]
];
?>
