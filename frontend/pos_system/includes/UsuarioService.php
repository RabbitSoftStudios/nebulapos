<?php
/**
 * Servicio de Usuarios
 * ====================
 * Capa de servicio para manejar usuarios en Supabase.
 * Soporta multiempresa mediante UUID.
 */

require_once __DIR__ . '/supabase.php';

class UsuarioService {
    private $db;
    private $table = 'usuarios';

    public function __construct() {
        $this->db = supabase($this->table);
    }

    /**
     * Obtener usuarios filtrados por empresa_uuid
     */
    public function getByEmpresa($empresaUuid) {
        if (empty($empresaUuid)) {
            return ['success' => false, 'error' => 'UUID de empresa requerido', 'data' => []];
        }

        $result = $this->db->select('*', ['empresa_uuid' => $empresaUuid]);
        
        if ($result['success']) {
            return ['success' => true, 'data' => $result['data'] ?? []];
        }
        
        return ['success' => false, 'error' => $result['error'] ?? 'Error al obtener usuarios', 'data' => []];
    }

    /**
     * Obtener un usuario por ID
     */
    public function getById($id) {
        if (empty($id)) {
            return ['success' => false, 'error' => 'ID requerido'];
        }

        $result = $this->db->select('*', ['id' => $id], 1);
        
        if ($result['success'] && !empty($result['data'])) {
            return ['success' => true, 'data' => $result['data'][0]];
        }
        
        return ['success' => false, 'error' => $result['error'] ?? 'Usuario no encontrado'];
    }

    /**
     * Crear un nuevo usuario
     */
    public function create($data) {
        if (empty($data)) {
            return ['success' => false, 'error' => 'Datos de usuario requeridos'];
        }

        // Validar campos requeridos
        $required = ['nombre', 'email', 'empresa_uuid'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'error' => "Campo requerido: $field"];
            }
        }

        // Si hay contraseña, hashearla
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        return $this->db->insert($data);
    }

    /**
     * Actualizar un usuario
     */
    public function update($id, $data) {
        if (empty($id) || empty($data)) {
            return ['success' => false, 'error' => 'ID y datos requeridos'];
        }

        // Si hay contraseña, hashearla
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        // No permitir actualizar el ID o empresa_uuid
        unset($data['id']);
        unset($data['empresa_uuid']);

        return $this->db->update($data, ['id' => $id]);
    }

    /**
     * Eliminar un usuario
     */
    public function delete($id) {
        if (empty($id)) {
            return ['success' => false, 'error' => 'ID requerido'];
        }

        return $this->db->delete(['id' => $id]);
    }

    /**
     * Verificar credenciales de usuario
     */
    public function verifyCredentials($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email y contraseña requeridos'];
        }

        $result = $this->db->select('*', ['email' => $email], 1);
        
        if ($result['success'] && !empty($result['data'])) {
            $user = $result['data'][0];
            
            if (password_verify($password, $user['password_hash'] ?? '')) {
                // No devolver el hash de contraseña
                unset($user['password_hash']);
                return ['success' => true, 'data' => $user];
            }
        }
        
        return ['success' => false, 'error' => 'Credenciales inválidas'];
    }
}
