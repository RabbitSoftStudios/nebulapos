<?php
// pos_system/includes/database.php
/**
 * Conexión y Configuración de Supabase
 * =====================================
 * Inicializa la configuración de conexión a Supabase.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Se asume que constants.php y functions.php ya han sido cargados
require_once INCLUDES_PATH . '/functions.php'; // Para load_env y getenv()

// Variables de entorno esperadas (cargadas vía load_env() en functions.php)
$supabase_url = getenv('SUPABASE_URL');
$supabase_anon_key = getenv('SUPABASE_ANON_KEY');
$supabase_service_role_key = getenv('SUPABASE_SERVICE_ROLE_KEY');

// =================================================================================
// GUÍA ARQUITECTÓNICA SENIOR (Compatibilidad Universal)
// =================================================================================
/*
 * Para que el sistema sea compatible con Farmacias/Abarrotes (Vencimiento) y
 * Restaurantes (Recetas/Componentes), se recomienda un diseño normalizado:
 *
 * 1. pos_products (Maestro): Define el nombre, SKU y precio de venta.
 * - Columna Clave: `product_type` ENUM ('finished_good', 'raw_material', 'recipe').
 *
 * 2. pos_inventory_lots: Maneja el inventario con vencimiento.
 * - Columnas: `product_id` (FK a pos_products), `batch_number`, `expiration_date`, `stock_qty`.
 * - *Uso:* Farmacias y Abarrotes registran el stock aquí. Los Restaurantes usan 
 * este para sus materias primas (`raw_material`).
 *
 * 3. pos_recipe_components: Descompone los productos tipo 'recipe'.
 * - Columnas: `recipe_id` (FK a pos_products donde product_type='recipe'), 
 * `component_id` (FK a pos_products donde product_type='raw_material'), `quantity_needed`.
 * - *Uso:* Permite calcular el stock virtual de un plato (receta) y descontar 
 * de los componentes al venderse.
 *
 * 4. pos_sales_items: Almacena la venta.
 * - Columna Clave: `lot_id` (FK a pos_inventory_lots).
 * - *Uso:* Necesario para rastrear el vencimiento de los productos vendidos 
 * (e.g., para retiros o garantías).
 */
// =================================================================================

/**
 * Clase para manejar la configuración de Supabase y sus llamadas API.
 * En un proyecto PHP puro, esto actúa como una capa simple de abstracción sobre cURL.
 */
class SupabaseAPI {
    private static $url;
    private static $anon_key;
    private static $service_role_key;

    public static function init() {
        global $supabase_url, $supabase_anon_key, $supabase_service_role_key;
        self::$url = $supabase_url;
        self::$anon_key = $supabase_anon_key;
        self::$service_role_key = $supabase_service_role_key;

        if (empty(self::$url) || empty(self::$anon_key)) {
             Logger::log_error("FATAL ERROR: Las claves de Supabase no están definidas.");
             // No detiene la ejecución aquí, solo genera un error. La vista/controlador debe manejar esto.
        }
    }
    
    /**
     * Realiza una solicitud genérica a la API de Supabase (PostgREST).
     * @param string $method GET, POST, PUT, DELETE
     * @param string $endpoint El nombre de la tabla o función RPC.
     * @param array $data Cuerpos de datos para POST/PUT.
     * @param bool $use_service_key Usa la clave de Service Role (para operaciones backend).
     * @return array La respuesta decodificada o un array de error.
     */
    public static function request(string $method, string $endpoint, array $data = [], bool $use_service_key = false): array {
        if (empty(self::$url)) {
            return ['error' => 'Configuración de Supabase no inicializada.', 'status' => HTTP_INTERNAL_SERVER_ERROR];
        }

        $url = rtrim(self::$url, '/') . '/rest/v1/' . ltrim($endpoint, '/');
        $key = $use_service_key ? self::$service_role_key : self::$anon_key;
        $auth_header = 'Authorization: Bearer ' . $key;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $key,
            $auth_header
        ]);

        if (!empty($data) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            Logger::log_error("Supabase API Error ({$method} {$endpoint}): cURL Falló: " . $curl_error);
            return ['error' => 'Error de conexión a la API: ' . $curl_error, 'status' => HTTP_INTERNAL_SERVER_ERROR];
        }

        $decoded_response = json_decode($response, true);
        
        if ($http_code < 200 || $http_code >= 300) {
            $error_message = $decoded_response['message'] ?? 'Error desconocido de Supabase.';
            Logger::log_error("Supabase API Error ({$method} {$endpoint}) - HTTP {$http_code}: " . $error_message);
            return ['error' => $error_message, 'status' => $http_code];
        }

        return $decoded_response;
    }
}

// Inicialización de la clase API
SupabaseAPI::init();