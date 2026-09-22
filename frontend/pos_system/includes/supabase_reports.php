<?php
/**
 * Sistema POS - Cliente Supabase
 * ================================
 * Maneja la conexión y operaciones con Supabase PostgreSQL
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */
// VER 2.0

declare(strict_types=1);
require_once __DIR__ . '/../../../vendor/autoload.php';

// Incluir el inicializador ANTES de la clase
require_once __DIR__ . '/init_report.php';

class SupabaseClient {
    private string $url;
    private string $key;
    private ?string $jwt;
    private ?string $table;
    
    public function __construct(?string $table = null) {
        if (!defined('SUPABASE_URL') || !defined('SUPABASE_KEY')) {
            throw new RuntimeException('Supabase configuration constants are not defined');
        }
        
        $this->url = rtrim(SUPABASE_URL, '/');
        $this->key = SUPABASE_KEY;
        $this->jwt = $this->getJWT();
        $this->table = $table;
        
        if ($table !== null && !preg_match('/^[a-zA-Z0-9_-]+$/', $table)) {
            throw new InvalidArgumentException('Invalid table name');
        }
    }
    
    /**
     * Obtiene el nombre de la tabla actual
     */
    public function getTableName(): ?string {
        return $this->table;
    }
    
    /**
     * Obtiene JWT de autenticación
     */
    private function getJWT(): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['supabase_jwt'] ?? $this->key;
    }
    
    /**
     * Ejecuta una consulta SELECT con tipos estrictos
     */
    public function select(
        string $columns = '*', 
        array $filters = [], 
        ?int $limit = null, 
        ?int $offset = null
    ): array {
        if ($this->table === null) {
            throw new RuntimeException('Table name is not set');
        }
        
        $endpoint = $this->url . '/rest/v1/' . $this->table;
        
        $query = [];
        if ($columns !== '*') {
            $query['select'] = $this->sanitizeColumns($columns);
        }
        
        foreach ($filters as $key => $value) {
            if (!is_string($key) || !is_scalar($value)) {
                continue;
            }
            $query[$key] = 'eq.' . urlencode((string)$value);
        }
        
        if ($limit !== null && $limit > 0) {
            $query['limit'] = (string)$limit;
        }
        
        if ($offset !== null && $offset >= 0) {
            $query['offset'] = (string)$offset;
        }
        
        return $this->makeRequest($endpoint, 'GET', null, false, $query);
    }
    
    /**
     * Inserta un nuevo registro
     */
    public function insert(array $data): array {
        if ($this->table === null) {
            throw new RuntimeException('Table name is not set');
        }
        
        $endpoint = $this->url . '/rest/v1/' . $this->table;
        return $this->makeRequest($endpoint, 'POST', $data, true);
    }
    
    /**
     * Actualiza registros
     */
    public function update(array $data, array $filters = []): array {
        if ($this->table === null) {
            throw new RuntimeException('Table name is not set');
        }
        
        $endpoint = $this->url . '/rest/v1/' . $this->table;
        
        $query = [];
        foreach ($filters as $key => $value) {
            if (!is_string($key) || !is_scalar($value)) {
                continue;
            }
            $query[$key] = 'eq.' . urlencode((string)$value);
        }
        
        return $this->makeRequest($endpoint, 'PATCH', $data, true, $query);
    }
    
    /**
     * Elimina registros
     */
    public function delete(array $filters = []): array {
        if ($this->table === null) {
            throw new RuntimeException('Table name is not set');
        }
        
        $endpoint = $this->url . '/rest/v1/' . $this->table;
        
        $query = [];
        foreach ($filters as $key => $value) {
            if (!is_string($key) || !is_scalar($value)) {
                continue;
            }
            $query[$key] = 'eq.' . urlencode((string)$value);
        }
        
        return $this->makeRequest($endpoint, 'DELETE', null, false, $query);
    }
    
    /**
     * Ejecuta una consulta personalizada
     */
    public function query(string $sql): array {
        $endpoint = $this->url . '/rest/v1/rpc/execute_sql';
        return $this->makeRequest($endpoint, 'POST', ['sql' => $sql], true);
    }
    
     /**
     * INICIO UPDATE PARA UUID
     */

    /**
     * Inserta un DTE con manejo automático de receptor_id y multiempresa
     */
    public function insertDTE(array $dteData, ?string $usuarioId = null, ?string $empresaNit = null): array {
        try {
            // 1. Validar datos básicos
            if (empty($dteData['identificacion']['codigoGeneracion'])) {
                throw new RuntimeException('Código de generación es requerido');
            }
            
            // 2. Obtener o crear receptor_id
            $receptorId = $this->obtenerReceptorId($dteData);
            
            // 3. Preparar datos para inserción
            $datosInsertar = [
                'codigo_generacion' => $dteData['identificacion']['codigoGeneracion'],
                'emisor_nit' => $empresaNit ?? $dteData['emisor']['nit'],
                'receptor_id' => $receptorId,
                'numero_control' => $dteData['identificacion']['numeroControl'],
                'tipo_dte' => $dteData['identificacion']['tipoDte'] ?? '01',
                'fecha_emision' => $this->formatDateTime(
                    $dteData['identificacion']['fecEmi'] ?? '',
                    $dteData['identificacion']['horEmi'] ?? ''
                ),
                'total_pagar' => $dteData['resumen']['totalPagar'] ?? 0,
                'documento_json' => json_encode($dteData),
                'estado_firma' => 'pendiente',
                'origen' => 'interno',
                'metadata' => json_encode([
                    'usuario_id' => $usuarioId,
                    'empresa_nit' => $empresaNit ?? $dteData['emisor']['nit'],
                    'metodo_pago' => ($dteData['resumen']['condicionOperacion'] == 1) ? 'Efectivo' : 'Tarjeta',
                    'fecha_insercion' => date('c'),
                    'cliente_nombre' => $dteData['receptor']['nombre'] ?? 'CONSUMIDOR FINAL'
                ])
            ];
            
            // 4. Validar campos obligatorios
            $this->validarDatosDTE($datosInsertar);
            
            // 5. Insertar en dte_facturas
            return $this->insert($datosInsertar);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error en insertDTE: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Inserta una venta en pos_ventas
     */
    public function insertVenta(array $ventaData): array {
        if ($this->table !== 'pos_ventas') {
            $originalTable = $this->table;
            $this->table = 'pos_ventas';
        }
        
        $result = $this->insert($ventaData);
        
        if (isset($originalTable)) {
            $this->table = $originalTable;
        }
        
        return $result;
    }

    /**
     * Ejecuta RPC en Supabase
     */
    public function rpc(string $functionName, array $params = []): array {
        $endpoint = $this->url . '/rest/v1/rpc/' . $functionName;
        return $this->makeRequest($endpoint, 'POST', $params, true);
    }

    /**
     * Obtiene o crea receptor_id
     */
    private function obtenerReceptorId(array $dteData): string {
        // Si el receptor tiene NIT
        if (!empty($dteData['receptor']['nit']) && $dteData['receptor']['nit'] !== 'CF') {
            return $this->obtenerOCrearReceptor(
                $dteData['receptor']['nit'],
                $dteData['receptor']['nombre'] ?? 'CLIENTE ' . $dteData['receptor']['nit']
            );
        }
        
        // Consumidor Final (NIT CF o vacío)
        return $this->obtenerReceptorDefault();
    }

    /**
     * Obtiene o crea un receptor
     */
    private function obtenerOCrearReceptor(string $nit, string $nombre): string {
        // Buscar receptor existente
        $receptor = $this->buscarReceptorPorNit($nit);
        
        if ($receptor) {
            return $receptor['id'];
        }
        
        // Crear nuevo receptor
        return $this->crearReceptor([
            'nit' => $nit,
            'nombre' => $nombre,
            'tipo' => $this->determinarTipoReceptor($nit),
            'created_at' => date('c'),
            'updated_at' => date('c')
        ]);
    }

    /**
     * Busca receptor por NIT
     */
    private function buscarReceptorPorNit(string $nit): ?array {
        $tempTable = $this->table;
        $this->table = 'dte_receptores';
        
        $result = $this->select('*', ['nit' => $nit], 1);
        
        $this->table = $tempTable;
        
        return $result['success'] && !empty($result['data']) ? $result['data'][0] : null;
    }

    /**
     * Crea un nuevo receptor
     */
    private function crearReceptor(array $datosReceptor): string {
        // Estructura de la tabla actual
        $estructuraReceptor = [
            'id' => $datosReceptor['id'] ?? $this->generarUUID(),
            'nit' => $datosReceptor['nit'],
            'nombre' => $datosReceptor['nombre'],
            'tipo' => $datosReceptor['tipo'] ?? 'CF',
            'empresa_nit' => $datosReceptor['empresa_nit'] ?? null,
            'usuario_id' => $datosReceptor['usuario_id'] ?? null,
            'is_global' => $datosReceptor['is_global'] ?? false,
            'cod_actividad' => $datosReceptor['cod_actividad'] ?? null,
            'direccion' => isset($datosReceptor['direccion']) ? 
                json_encode($datosReceptor['direccion']) : null,
            'correo' => $datosReceptor['correo'] ?? null,
            'telefono' => $datosReceptor['telefono'] ?? null,
            'metadata' => isset($datosReceptor['metadata']) ? 
                json_encode($datosReceptor['metadata']) : null,
            'created_at' => $datosReceptor['created_at'] ?? date('c'),
            'updated_at' => $datosReceptor['updated_at'] ?? date('c')
        ];
        
        $tempTable = $this->table;
        $this->table = 'dte_receptores';
        
        $result = $this->insert($estructuraReceptor);
        
        $this->table = $tempTable;
        
        return $result['success'] ? 
            ($result['data'][0]['id'] ?? $estructuraReceptor['id']) : 
            $this->obtenerReceptorDefault();
    }

    private function determinarTipoReceptor(string $nit): string {
        if (empty($nit) || $nit === 'CF') {
            return 'CF';
        } elseif (strlen($nit) > 14 || preg_match('/[A-Za-z]/', $nit)) {
            return 'E'; // Extranjero
        } else {
            return 'N'; // Nacional
        }
    }

    private function obtenerReceptorDefault(): string {
        // UUID del receptor por defecto (Consumidor Final)
        return '00000000-0000-0000-0000-000000000001';
    }

    private function generarUUID(): string {
        // PHP 7+
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Formatea fecha y hora para PostgreSQL
     */
    private function formatDateTime(string $fecha, string $hora): string {
        if (empty($fecha)) {
            return date('c');
        }
        
        // Formato esperado: YYYY-MM-DD HH:MM:SS
        $fecha = trim($fecha);
        $hora = trim($hora);
        
        if (empty($hora)) {
            $hora = '00:00:00';
        }
        
        // Si la fecha ya está en formato ISO, devolverla
        if (strpos($fecha, '-') !== false && strpos($fecha, 'T') !== false) {
            return $fecha;
        }
        
        // Convertir fecha DD/MM/YYYY a YYYY-MM-DD
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' ' . $hora;
        }
        
        return $fecha . ' ' . $hora;
    }

    /**
     * Valida datos del DTE
     */
    private function validarDatosDTE(array $datos): void {
        $camposRequeridos = [
            'codigo_generacion',
            'emisor_nit',
            'receptor_id',
            'numero_control',
            'tipo_dte',
            'fecha_emision',
            'total_pagar',
            'documento_json'
        ];
        
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || (is_string($datos[$campo]) && trim($datos[$campo]) === '')) {
                throw new RuntimeException("Campo requerido faltante o vacío: $campo");
            }
        }
        
        // Validar que receptor_id sea un UUID válido
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $datos['receptor_id'])) {
            throw new RuntimeException("receptor_id no es un UUID válido: " . $datos['receptor_id']);
        }
    }

    
    /**
     * Realiza la petición HTTP con manejo robusto de errores
     */
    private function makeRequest(
        string $url, 
        string $method = 'GET', 
        ?array $data = null, 
        bool $isJson = false, 
        array $query = []
    ): array {
        $validMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        if (!in_array(strtoupper($method), $validMethods, true)) {
            return [
                'success' => false,
                'error' => 'Método HTTP no válido: ' . $method
            ];
        }
        
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        
        $ch = curl_init();
        
        if ($ch === false) {
            return [
                'success' => false,
                'error' => 'No se pudo inicializar cURL'
            ];
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        
        // SOLUCIÓN PARA ERROR SSL - OPCIONES SEGURAS
        $isLocalDevelopment = $this->isLocalDevelopment();
        
        if ($isLocalDevelopment) {
            // Para desarrollo local: desactivar verificación SSL (SOLO DESARROLLO)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            
            // Configuración adicional para desarrollo
            curl_setopt($ch, CURLOPT_VERBOSE, false); // Cambia a true para debug
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        } else {
            // Para producción: verificación SSL estricta
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            
            // Usar certificados del sistema
            $certPath = $this->getSystemCertPath();
            if ($certPath && file_exists($certPath)) {
                curl_setopt($ch, CURLOPT_CAINFO, $certPath);
            }
        }
        
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . ($this->jwt ?? $this->key),
            'Prefer: return=representation'
        ];
        
        if ($isJson && $data !== null) {
            $headers[] = 'Content-Type: application/json';
            try {
                $jsonData = json_encode($data, JSON_THROW_ON_ERROR);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            } catch (JsonException $e) {
                curl_close($ch);
                return [
                    'success' => false,
                    'error' => 'Error al codificar JSON: ' . $e->getMessage()
                ];
            }
        } elseif ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        // Para debug SSL (opcional, descomentar si necesitas más información)
        // curl_setopt($ch, CURLOPT_CERTINFO, true);
        // curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $sslErrors = curl_errno($ch) === CURLE_SSL_CACERT || 
                    curl_errno($ch) === CURLE_SSL_CACERT_BADFILE;
        
        curl_close($ch);
        
        // Si hay error SSL y estamos en desarrollo, intentar método alternativo
        if ($sslErrors && $isLocalDevelopment) {
            return $this->fallbackHttpRequest($url, $method, $data, $isJson, $query);
        }
        
        if ($error) {
            return [
                'success' => false,
                'code' => $httpCode,
                'data' => null,
                'error' => 'Error cURL: ' . $error . ' (SSL: ' . ($sslErrors ? 'Sí' : 'No') . ')'
            ];
        }
        
        $result = null;
        if ($response !== false && $response !== '') {
            try {
                $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                return [
                    'success' => false,
                    'code' => $httpCode,
                    'data' => $response,
                    'error' => 'Error decodificando JSON: ' . $e->getMessage()
                ];
            }
        }
        
        $success = ($httpCode >= 200 && $httpCode < 300);
        
        return [
            'success' => $success,
            'code' => $httpCode,
            'data' => $result,
            'error' => $success ? null : ($result['message'] ?? $result['error'] ?? 'Error desconocido')
        ];
    }

    /**
     * Detecta si está en entorno de desarrollo local
     */
    private function isLocalDevelopment(): bool {
        // Verificar por nombre de host
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $isLocal = stripos($host, 'localhost') !== false || 
                stripos($host, '127.0.0.1') !== false ||
                stripos($host, '.local') !== false ||
                stripos($host, '192.168.') !== false;
        
        // Verificar por IP
        if (!$isLocal) {
            $ip = $_SERVER['SERVER_ADDR'] ?? $_SERVER['LOCAL_ADDR'] ?? '';
            $isLocal = $ip === '127.0.0.1' || substr($ip, 0, 8) === '192.168.';
        }
        
        // Verificar por variable de entorno
        if (defined('ENVIRONMENT')) {
            return ENVIRONMENT === 'development';
        }
        
        return $isLocal;
    }

    /**
     * Obtiene ruta de certificados del sistema
     */
    private function getSystemCertPath(): ?string {
        $paths = [
            // Windows (WAMP)
            'C:\\wamp64\\bin\\php\\php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '\\extras\\ssl\\cacert.pem',
            'C:\\wamp\\bin\\php\\php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '\\extras\\ssl\\cacert.pem',
            'C:\\xampp\\php\\extras\\ssl\\cacert.pem',
            
            // Linux/Unix
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/usr/share/ssl/certs/ca-bundle.crt',
            '/usr/local/share/certs/ca-root-nss.crt',
            
            // macOS
            '/usr/local/etc/openssl/cert.pem',
            '/opt/homebrew/etc/openssl/cert.pem',
            
            // Alternativa: usar el incluido en este proyecto
            __DIR__ . '/cacert.pem'
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    /**
     * Método de respaldo usando file_get_contents (solo para desarrollo)
     */
    private function fallbackHttpRequest(
        string $url, 
        string $method = 'GET', 
        ?array $data = null, 
        bool $isJson = false, 
        array $query = []
    ): array {
        if (!$this->isLocalDevelopment()) {
            return [
                'success' => false,
                'error' => 'No se puede usar fallback en producción'
            ];
        }
        
        // Configurar contexto para ignorar SSL en desarrollo
        $options = [
            'http' => [
                'method' => $method,
                'header' => [
                    'apikey: ' . $this->key,
                    'Authorization: Bearer ' . ($this->jwt ?? $this->key),
                    'Prefer: return=representation'
                ],
                'ignore_errors' => true,
                'timeout' => 30
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        
        if ($isJson && $data !== null) {
            $options['http']['header'][] = 'Content-Type: application/json';
            $options['http']['content'] = json_encode($data);
        } elseif ($data !== null) {
            $options['http']['content'] = http_build_query($data);
        }
        
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        
        $context = stream_context_create($options);
        
        try {
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                return [
                    'success' => false,
                    'error' => 'Error en fallback HTTP request'
                ];
            }
            
            // Obtener código HTTP
            $httpCode = 200;
            if (isset($http_response_header[0])) {
                preg_match('/HTTP\/[0-9\.]+\s+([0-9]+)/', $http_response_header[0], $matches);
                $httpCode = $matches[1] ?? 200;
            }
            
            $result = json_decode($response, true);
            
            return [
                'success' => ($httpCode >= 200 && $httpCode < 300),
                'code' => (int)$httpCode,
                'data' => $result,
                'error' => ($httpCode >= 400) ? $result['message'] ?? 'Error desconocido' : null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Excepción en fallback: ' . $e->getMessage()
            ];
        }
    }
    
    
    /**
     * Sanitiza nombres de columnas para seguridad
     */
    private function sanitizeColumns(string $columns): string {
        $columnArray = explode(',', $columns);
        $sanitized = [];
        
        foreach ($columnArray as $column) {
            $column = trim($column);
            if (preg_match('/^[a-zA-Z0-9_, ]+$/', $column)) {
                $sanitized[] = $column;
            }
        }
        
        return implode(',', $sanitized);
    }
    
    /**
     * Sube un archivo a Supabase Storage
     */
    public function uploadFile(string $bucket, string $path, string $file): array {
        if (!file_exists($file) || !is_readable($file)) {
            return [
                'success' => false,
                'error' => 'Archivo no existe o no es legible'
            ];
        }
        
        $endpoint = $this->url . '/storage/v1/object/' . $bucket . '/' . ltrim($path, '/');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        $mimeType = mime_content_type($file) ?: 'application/octet-stream';
        $headers = [
            'Authorization: Bearer ' . ($this->jwt ?? $this->key),
            'Content-Type: ' . $mimeType,
            'Content-Length: ' . filesize($file)
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file));
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        }
        
        $result = null;
        if ($response !== false && $response !== '') {
            try {
                $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                return [
                    'success' => false,
                    'data' => $response,
                    'error' => 'Error decodificando JSON: ' . $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'code' => $httpCode,
            'data' => $result,
            'error' => ($httpCode >= 400) ? $result['message'] ?? 'Error desconocido' : null
        ];
    }
}

// Helper functions con tipado estricto
function supabase(?string $table = null): SupabaseClient {
    static $instances = [];
    
    $key = $table ?? 'default';
    if (!isset($instances[$key])) {
        $instances[$key] = new SupabaseClient($table);
    }
    
    return $instances[$key];
}

function getProductByBarcode(string $barcode): ?array {
    if (empty($barcode)) {
        return null;
    }
    
    $db = supabase(TABLE_PRODUCTS);
    $result = $db->select('*', ['codigo_barras' => $barcode], 1);
    
    if ($result['success'] && !empty($result['data']) && is_array($result['data'])) {
        return $result['data'][0];
    }
    
    return null;
}

function saveSale(array $saleData): array {
    if (empty($saleData)) {
        return [
            'success' => false,
            'error' => 'Datos de venta vacíos'
        ];
    }
    
    $db = supabase(TABLE_SALES);
    return $db->insert($saleData);
}

function updateProductStock(int $productId, int $quantity): array {
    if ($productId <= 0) {
        return [
            'success' => false,
            'error' => 'ID de producto inválido'
        ];
    }
    
    $db = supabase(TABLE_PRODUCTS);
    
    // Obtener stock actual
    $product = $db->select('stock', ['id' => $productId], 1);
    
    if ($product['success'] && !empty($product['data']) && is_array($product['data'])) {
        $currentStock = (int)($product['data'][0]['stock'] ?? 0);
        $newStock = $currentStock - $quantity;
        
        if ($newStock < 0) {
            return [
                'success' => false,
                'error' => 'Stock insuficiente'
            ];
        }
        
        // Actualizar stock
        return $db->update(['stock' => $newStock], ['id' => $productId]);
    }
    
    return [
        'success' => false, 
        'error' => 'Producto no encontrado'
    ];
}

// Función para obtener productos frecuentes con manejo de errores
function getFrequentProducts(int $limit = 8): array {
    try {
        $db = supabase(TABLE_PRODUCTS);
        
        // Consulta para productos frecuentes - ajusta según tu esquema
        $result = $db->select('*', [], $limit);
        
        if (!$result['success']) {
            error_log('Error obteniendo productos frecuentes: ' . ($result['error'] ?? 'Desconocido'));
        }
        
        return $result;
    } catch (Exception $e) {
        error_log('Excepción en getFrequentProducts: ' . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Error del sistema: ' . $e->getMessage(),
            'data' => []
        ];
    }
}