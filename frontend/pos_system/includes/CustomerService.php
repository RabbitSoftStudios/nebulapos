<?php
/**
 * CustomerService - Servicio para gestión de clientes (mh_cliente_consumidor)
 * Soporta el esquema completo y maneja la transformación de datos.
 */

require_once __DIR__ . '/supabase.php';

class CustomerService {
    private $db;
    private $table = TABLE_CUSTOMERS; // mh_cliente_consumidor
    
    public function __construct() {
        $this->db = supabase($this->table);
    }
    
    /**
     * Transforma los datos del formulario al formato exacto de mh_cliente_consumidor
     */
    private function transformToTableFormat($data) {
        $transformed = [];
        
        // --- Nombres y Apellidos (Separación) ---
        if (!empty($data['nombres'])) {
            $nombres = explode(' ', trim((string)$data['nombres']), 2);
            $transformed['p_nombre'] = $nombres[0];
            $transformed['s_nombre'] = $nombres[1] ?? null;
        }
        
        if (!empty($data['apellidos'])) {
            $apellidos = explode(' ', trim((string)$data['apellidos']), 2);
            $transformed['p_apellido'] = $apellidos[0];
            $transformed['s_apellido'] = $apellidos[1] ?? null;
        }
        
        // --- Campos de Identificación (Strings Numéricos) ---
        // Se tratan como strings para evitar errores de rango (integer out of range)
        $idFields = ['dui', 'nit', 'nrc', 'telefono', 'celular', 'cod_postal'];
        
        foreach ($idFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                // Limpiar todo lo que no sea número pero mantener como STRING
                $transformed[$field] = preg_replace('/[^0-9]/', '', (string)$data[$field]);
            }
        }
        
        // --- Campos Numéricos (Enteros Cortos/IDs) ---
        $intFields = [
            'tipo_persona', 'departamento', 'municipio', 'pais', 
            'domicilio_fiscal', 'cod_giro'
        ];
        
        foreach ($intFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $val = preg_replace('/[^0-9]/', '', (string)$data[$field]);
                $transformed[$field] = $val !== '' ? (int)$val : null;
            }
        }
        
        // --- Campos de Texto (String) ---
        $stringFields = [
            'nombre_comercial', 'direccion', 'complemento', 
            'codestablemh', 'codestable', 'codpuntoventamh', 
            'codpuntoventa', 'email', 'tipo_contribuyente'
        ];
        
        foreach ($stringFields as $field) {
            if (isset($data[$field])) {
                $transformed[$field] = trim((string)$data[$field]);
            }
        }
        
        // --- Campos Especiales ---
        if (isset($data['user_id'])) {
            $transformed['user_id'] = $data['user_id'];
        }
        
        // --- Manejo del error de columna 'active' ---
        // Si el esquema de Supabase no ha refrescado, el campo 'active' puede dar error.
        // Lo enviamos solo si viene explícitamente y es parte de una actualización o si queremos forzarlo.
        // En creación (INSERT), la base de datos ya tiene un DEFAULT true.
        if (isset($data['active'])) {
            $transformed['active'] = (bool)$data['active'];
        }
        
        return $transformed;
    }
    
    /**
     * Crear un nuevo cliente
     */
    public function create($data) {
        if (empty($data['nombres']) || empty($data['apellidos'])) {
            return ['success' => false, 'error' => 'Nombres y Apellidos son requeridos'];
        }
        
        try {
            $customerData = $this->transformToTableFormat($data);
            
            // NOTA: Para superar el error "Could not find the 'active' column",
            // quitamos 'active' del insert inicial para que use el DEFAULT de la BD.
            if (isset($customerData['active']) && $customerData['active'] === true) {
                unset($customerData['active']);
            }
            
            return $this->db->insert($customerData);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Obtener un cliente por ID
     */
    public function getById($id) {
        try {
            $result = $this->db->select('*', ['id' => $id], 1);
            if ($result['success'] && !empty($result['data'])) {
                $cliente = $result['data'][0];
                
                // Re-ensamblar nombres para la UI
                $cliente['nombres'] = trim(($cliente['p_nombre'] ?? '') . ' ' . ($cliente['s_nombre'] ?? ''));
                $cliente['apellidos'] = trim(($cliente['p_apellido'] ?? '') . ' ' . ($cliente['s_apellido'] ?? ''));
                
                return ['success' => true, 'data' => $cliente];
            }
            return ['success' => false, 'error' => 'Cliente no encontrado'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Actualizar un cliente
     */
    public function update($id, $data) {
        if (empty($id)) {
            return ['success' => false, 'error' => 'ID es requerido'];
        }
        
        try {
            $updateData = $this->transformToTableFormat($data);
            return $this->db->update($updateData, ['id' => $id]);
        } catch (Exception $e) {
            // Si el error es por la columna 'active', lo intentamos de nuevo sin ella
            if (strpos($e->getMessage(), "'active'") !== false && isset($updateData['active'])) {
                unset($updateData['active']);
                return $this->db->update($updateData, ['id' => $id]);
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Desactivar cliente (Soft delete)
     */
    public function delete($id) {
        try {
            return $this->db->update(['active' => false], ['id' => $id]);
        } catch (Exception $e) {
            // Si la columna 'active' no responde, informamos del problema de esquema
            return ['success' => false, 'error' => 'No se pudo desactivar el cliente. El esquema de la API puede estar desactualizado: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los clientes activos
     */
    public function getAll($filters = []) {
        try {
            // Intentamos traer solo los activos
            $params = [];
            
            // Si la columna 'active' da problemas, este select podría fallar.
            // En ese caso el usuario debería usar un RPC o refrescar el esquema en Supabase (Settings > API > Save).
            $result = $this->db->select('*', ['active' => 'true']);
            
            if (!$result['success'] && strpos(($result['error'] ?? ''), "'active'") !== false) {
                // Fallback: traer todos si 'active' falla
                $result = $this->db->select('*');
            }
            
            if ($result['success'] && !empty($result['data'])) {
                foreach ($result['data'] as &$cliente) {
                    $cliente['nombres'] = trim(($cliente['p_nombre'] ?? '') . ' ' . ($cliente['s_nombre'] ?? ''));
                    $cliente['apellidos'] = trim(($cliente['p_apellido'] ?? '') . ' ' . ($cliente['s_apellido'] ?? ''));
                }
            }
            
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}