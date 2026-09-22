<?php
/**
 * Servicio de Empresas
 * ====================
 * Capa de servicio para manejar la información de la empresa en Supabase.
 */

require_once __DIR__ . '/supabase.php';

class EmpresaService {
    private $db;
    private $table = 'empresas';

    public function __construct() {
        $this->db = supabase($this->table);
    }

    /**
     * Obtener la información de la empresa.
     * Si no hay filtros, trae la primera empresa encontrada (asumiendo configuración única).
     * Si hay user_id en sesión, filtra por ese user_id.
     */
    public function get($filters = []) {
        // Si no hay filtros y hay user_id en sesión, filtrar por user_id
        if (empty($filters) && session_status() === PHP_SESSION_ACTIVE) {
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId) {
                $filters['user_id'] = $userId;
            }
        }
        
        $result = $this->db->select('*', $filters, 1);
        if ($result['success'] && !empty($result['data'])) {
            return ['success' => true, 'data' => $result['data'][0]];
        }
        return ['success' => false, 'error' => $result['error'] ?? 'Empresa no encontrada', 'data' => null];
    }

    /**
     * Crear o actualizar la información de la empresa.
     */
    public function update($data, $filters = []) {
        if (empty($data)) {
            return ['success' => false, 'error' => 'No hay datos para actualizar'];
        }

        // Si hay un ID o filtros, actualizamos. 
        if (!empty($filters) || !empty($data['id'])) {
            $updateFilters = !empty($filters) ? $filters : ['id' => $data['id']];
            unset($data['id']); // No actualizar el ID
            return $this->db->update($data, $updateFilters);
        } else {
            // Si no hay filtros ni ID, intentamos insertar (o el controlador debería manejar la lógica)
            return $this->db->insert($data);
        }
    }
}
