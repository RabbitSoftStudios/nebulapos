<?php
/**
 * Servicio de Clientes
 * ====================
 * Capa de servicio para manejar la lógica de negocio de clientes y la interacción con Supabase.
 */

require_once __DIR__ . '/supabase.php';

class CustomerService {
    private $db;
    private $table = TABLE_CUSTOMERS; // Usar constante definida en constants.php

    public function __construct() {
        $this->db = supabase($this->table);
    }

    /**
     * Obtener todos los clientes activos
     */
    public function getAll() {
        // Podríamos filtrar por active=true si fuera necesario, o traer todos
        // Por ahora traemos todos para la gestión
        return $this->db->select('*');
    }

    /**
     * Obtener un cliente por ID
     */
    public function getById($id) {
        $result = $this->db->select('*', ['id' => $id], 1);
        if ($result['success'] && !empty($result['data'])) {
            return ['success' => true, 'data' => $result['data'][0]];
        }
        return ['success' => false, 'error' => 'Cliente no encontrado'];
    }

    /**
     * Crear un nuevo cliente
     */
    public function create($data) {
        // Validación básica
        if (empty($data['nombres']) || empty($data['apellidos'])) {
            return ['success' => false, 'error' => 'Nombres y Apellidos son requeridos'];
        }

        // Preparar datos para dte_clientes
        $customerData = [
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'dui' => $data['dui'] ?? null,
            'nit' => $data['nit'] ?? null,
            'nrc' => $data['nrc'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'email' => $data['email'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'municipio' => $data['municipio'] ?? null,
            'departamento' => $data['departamento'] ?? null
            //'active' => isset($data['active']) ? (bool)$data['active'] : true
        ];

        return $this->db->insert($customerData);
    }

    /**
     * Actualizar un cliente existente
     */
    public function update($id, $data) {
        if (empty($id)) {
            return ['success' => false, 'error' => 'ID es requerido'];
        }

        // Filtrar solo los campos permitidos que vengan en $data
        $allowedFields = [
            'nombres', 'apellidos', 'dui', 'nit', 'nrc', 
            'telefono', 'email', 'direccion', 'municipio', 
            'departamento'
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return ['success' => false, 'error' => 'No hay datos para actualizar'];
        }

        return $this->db->update($updateData, ['id' => $id]);
    }

    /**
     * Eliminar (o desactivar) un cliente
     */
    public function delete($id) {
        // En lugar de borrar físicamente, podríamos querer desactivarlo (soft delete).
        // Si el requerimiento es borrar físico:
        return $this->db->delete(['id' => $id]);
        
        // Si fuera soft delete:
        // return $this->db->update(['active' => false], ['id' => $id]);
    }
}
