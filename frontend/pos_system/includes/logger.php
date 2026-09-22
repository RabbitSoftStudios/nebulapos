<?php
// pos_system/includes/logger.php
/**
 * Módulo de Registro de Eventos (Logger)
 * ========================================
 * Clase estática para manejar el registro de eventos y errores en archivos locales.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

class Logger {
    private static $log_file_prefix = LOGS_PATH . '/app';
    private static $error_file_prefix = LOGS_PATH . '/error';

    /**
     * Escribe un mensaje en el archivo de registro.
     * @param string $level Nivel del mensaje (INFO, WARN, ERROR, CRITICAL).
     * @param string $message El contenido del mensaje.
     */
    public static function log(string $level, string $message) {
        if (!defined('LOGS_PATH')) {
            // Falla catastrófica si no hay constantes
            error_log("FATAL: LOGS_PATH no definido. No se puede loguear.");
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] [{$level}] " . $message . PHP_EOL;
        
        // Determinar el archivo de log (rotación diaria simple)
        $date_suffix = date('Y-m-d');
        $target_file = self::$log_file_prefix . "_{$date_suffix}.log";
        
        // Asegurar que el directorio exista
        if (!is_dir(LOGS_PATH)) {
            mkdir(LOGS_PATH, 0777, true);
        }

        // Escribir en el log principal
        file_put_contents($target_file, $log_entry, FILE_APPEND | LOCK_EX);
        
        // Si es un error crítico, escribir también en el log de errores separado
        if (in_array($level, ['ERROR', 'CRITICAL', 'FATAL'])) {
            $error_file = self::$error_file_prefix . "_{$date_suffix}.log";
            file_put_contents($error_file, $log_entry, FILE_APPEND | LOCK_EX);
        }
    }

    /** Alias para log INFO. */
    public static function info(string $message) {
        self::log('INFO', $message);
    }
    
    /** Alias para log DEBUG (solo en desarrollo). */
    public static function debug(string $message) {
        if (defined('APP_ENV') && APP_ENV === 'development') {
            self::log('DEBUG', $message);
        }
    }

    /** Alias para log ERROR. */
    public static function log_error(string $message) {
        self::log('ERROR', $message);
    }
}

// ----------------------------------------------------
// Manejadores Globales de Errores y Excepciones
// ----------------------------------------------------

// Configuramos el manejador de errores de PHP
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return; // No está incluido en error_reporting
    }
    
    $level = 'ERROR';
    switch ($severity) {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $level = 'FATAL';
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            $level = 'WARN';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $level = 'NOTICE';
            break;
    }
    Logger::log($level, "PHP Error: {$message} en {$file}:{$line}");
    return true; // No ejecutar el manejador de errores interno de PHP
});

// Configuramos el manejador de excepciones
set_exception_handler(function (Throwable $exception) {
    Logger::log('CRITICAL', 
               "Excepción no capturada: " . $exception->getMessage() . 
               " | Archivo: " . $exception->getFile() . ":" . $exception->getLine() . 
               " | Trace: " . $exception->getTraceAsString());
               
    // Intenta enviar una respuesta de error JSON si es posible
    if (function_exists('send_json_error')) {
        $response_message = (defined('APP_ENV') && APP_ENV === 'production') ? 
                            'Ocurrió un error interno del servidor.' : 
                            'Excepción: ' . $exception->getMessage();
        send_json_error($response_message, HTTP_INTERNAL_SERVER_ERROR);
    } else {
        http_response_code(HTTP_INTERNAL_SERVER_ERROR);
        echo "Error crítico del sistema. Detalles registrados.";
    }
});