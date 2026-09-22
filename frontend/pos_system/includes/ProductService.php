<?php
/**
 * ProductService (PDO)
 * Team MYTS
 */

require_once __DIR__ . '/pg_connection.php';

class ProductService {

    private PDO $db;

    public function __construct() {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          'N/A',
          $_SESSION['cashier']['name'] ?? 'system'
        ));
        $this->db = PgConnection::get();
    }

    public function list(
        int $page = 1,
        int $perPage = 10,
        string $orderBy = 'id',
        string $direction = 'DESC'
    ): array {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          'N/A',
          $_SESSION['cashier']['name'] ?? 'system'
        ));

        // Columnas permitidas para ordenamiento (SEGURIDAD)
        $allowedColumns = [
            'id',
            'codigo_producto',
            'nombre_producto',
            'precio_venta',
            'stock_actual'
        ];

        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'id';
        }

        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT *
            FROM dte_productos
            WHERE borrado_logico = false
            ORDER BY {$orderBy} {$direction}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $total = (int) $this->db
            ->query("SELECT COUNT(*) FROM dte_productos WHERE borrado_logico = false")
            ->fetchColumn();

        return [
            'success' => true,
            'data' => $stmt->fetchAll(),
            'total' => $total
        ];
    }

    public function get(int $id): array {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          $id,
          $_SESSION['cashier']['name'] ?? 'system'
        ));
        $stmt = $this->db->prepare("
            SELECT * FROM dte_productos WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        return [
            'success' => true,
            'data' => $stmt->fetch()
        ];
    }

    public function getById(int $id): array {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          $id,
          $_SESSION['cashier']['name'] ?? 'system'
        ));
        return $this->get($id);
    }

    public function getByBarcode(string $barcode): array {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          'N/A',
          $_SESSION['cashier']['name'] ?? 'system'
        ));
        $stmt = $this->db->prepare("
            SELECT * FROM dte_productos WHERE codigo_barras = :barcode
        ");
        $stmt->execute(['barcode' => $barcode]);

        return [
            'success' => true,
            'data' => $stmt->fetch()
        ];
    }

    public function create(array $data): array {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          'N/A',
          $_SESSION['cashier']['name'] ?? 'system'
        ));
        $stmt = $this->db->prepare("
            INSERT INTO dte_productos
            (codigo_producto, nombre_producto, precio_venta, stock_actual)
            VALUES (:codigo, :nombre, :precio, :stock)
            RETURNING *
        ");

        $stmt->execute([
            'codigo' => $data['codigo_producto'],
            'nombre' => $data['nombre_producto'],
            'precio' => $data['precio_venta'],
            'stock'  => $data['stock_actual']
        ]);

        return [
            'success' => true,
            'data' => $stmt->fetch()
        ];
    }

    public function update(int $id, array $data): array {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          $id,
          $_SESSION['cashier']['name'] ?? 'system'
        ));
        $stmt = $this->db->prepare("
            UPDATE dte_productos
            SET nombre_producto = :nombre,
                precio_venta = :precio,
                stock_actual = :stock,
                actualizado_el = now()
            WHERE id = :id
            RETURNING *
        ");

        $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre_producto'],
            'precio' => $data['precio_venta'],
            'stock' => $data['stock_actual']
        ]);

        return [
            'success' => true,
            'data' => $stmt->fetch()
        ];
    }

    public function delete(int $id): array {
        error_log(sprintf(
          '[PRODUCTS] %s | ID:%s | USER:%s',
          __FUNCTION__,
          $id,
          $_SESSION['cashier']['name'] ?? 'system'
        ));
        $stmt = $this->db->prepare("
            UPDATE dte_productos
            SET borrado_logico = true
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        return ['success' => true];
    }
    
}
