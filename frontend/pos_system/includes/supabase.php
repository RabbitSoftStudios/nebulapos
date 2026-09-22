<?php
/**
 * NebulaPOS - adaptador de persistencia local SQLite.
 *
 * Este archivo conserva el nombre histórico SupabaseClient para mantener
 * compatibilidad temporal con los módulos existentes. No realiza llamadas
 * a Supabase ni requiere credenciales externas.
 */
declare(strict_types=1);

require_once __DIR__ . '/pg_connection.php';

final class SupabaseClient
{
    private PDO $pdo;
    private ?string $table;

    public function __construct(?string $table = null)
    {
        $this->pdo = pg_pool();
        $this->table = $this->validateTable($table);
    }

    public function getTableName(): ?string { return $this->table; }

    public function select(string $columns = '*', array $filters = [], ?int $limit = null, ?int $offset = null): array
    {
        $this->requireTable();
        $sql = 'SELECT ' . $this->sanitizeColumns($columns) . ' FROM ' . $this->table;
        $params = [];
        $where = $this->buildFilters($filters, $params);
        if ($where) $sql .= ' WHERE ' . $where;
        if ($limit !== null && $limit > 0) $sql .= ' LIMIT ' . (int)$limit;
        if ($offset !== null && $offset >= 0) $sql .= ' OFFSET ' . (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return ['success' => true, 'data' => $stmt->fetchAll()];
    }

    public function insert(array $data): array
    {
        $this->requireTable();
        if (!$data) throw new InvalidArgumentException('Insert data cannot be empty');
        $this->ensureColumns(array_keys($data));

        $columns = array_keys($data);
        $params = [];
        foreach ($columns as $column) $params[':' . $column] = $this->normalize($data[$column]);

        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_keys($params)) . ')';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $id = $this->pdo->lastInsertId();
        $row = [];
        if ($id !== '0' && $this->hasColumn('id')) {
            $q = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = ? LIMIT 1');
            $q->execute([$id]);
            $row = $q->fetch() ?: [];
        }
        return ['success' => true, 'data' => $row ? [$row] : [], 'id' => $id];
    }

    public function update(array $data, array $filters = []): array
    {
        $this->requireTable();
        if (!$data) return ['success' => true, 'data' => []];
        $this->ensureColumns(array_keys($data));

        $params = [];
        $sets = [];
        foreach ($data as $column => $value) {
            $p = ':set_' . $column;
            $sets[] = $column . ' = ' . $p;
            $params[$p] = $this->normalize($value);
        }
        $where = $this->buildFilters($filters, $params);
        if (!$where) throw new RuntimeException('UPDATE requires filters');

        $stmt = $this->pdo->prepare('UPDATE ' . $this->table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where);
        $stmt->execute($params);
        return ['success' => true, 'data' => [], 'affected' => $stmt->rowCount()];
    }

    public function delete(array $filters = []): array
    {
        $this->requireTable();
        $params = [];
        $where = $this->buildFilters($filters, $params);
        if (!$where) throw new RuntimeException('DELETE requires filters');
        $stmt = $this->pdo->prepare('DELETE FROM ' . $this->table . ' WHERE ' . $where);
        $stmt->execute($params);
        return ['success' => true, 'data' => [], 'affected' => $stmt->rowCount()];
    }

    public function query(string $sql): array
    {
        $stmt = $this->pdo->query($sql);
        return ['success' => true, 'data' => $stmt->fetchAll()];
    }

    public function insertVenta(array $ventaData): array
    {
        $original = $this->table;
        $this->table = 'pos_ventas';
        try { return $this->insert($ventaData); }
        finally { $this->table = $original; }
    }

    public function insertDTE(array $dteData, ?string $usuarioId = null, ?string $empresaNit = null): array
    {
        $codigo = $dteData['identificacion']['codigoGeneracion'] ?? null;
        if (!$codigo) return ['success' => false, 'error' => 'Código de generación es requerido'];

        $original = $this->table;
        $this->table = 'dte_facturas';
        try {
            return $this->insert([
                'codigo_generacion' => $codigo,
                'emisor_nit' => $empresaNit ?? ($dteData['emisor']['nit'] ?? null),
                'numero_control' => $dteData['identificacion']['numeroControl'] ?? null,
                'tipo_dte' => $dteData['identificacion']['tipoDte'] ?? '01',
                'fecha_emision' => ($dteData['identificacion']['fecEmi'] ?? date('Y-m-d')) . ' ' . ($dteData['identificacion']['horEmi'] ?? date('H:i:s')),
                'total_pagar' => $dteData['resumen']['totalPagar'] ?? 0,
                'documento_json' => json_encode($dteData, JSON_UNESCAPED_UNICODE),
                'estado_firma' => 'pendiente',
                'origen' => 'interno',
                'usuario_id' => $usuarioId,
                'metadata' => json_encode(['empresa_nit' => $empresaNit], JSON_UNESCAPED_UNICODE)
            ]);
        } finally { $this->table = $original; }
    }

    public function rpc(string $functionName, array $params = []): array
    {
        throw new RuntimeException('RPC Supabase eliminado: ' . $functionName);
    }

    private function requireTable(): void { if (!$this->table) throw new RuntimeException('Table name is not set'); }

    private function validateTable(?string $table): ?string
    {
        if ($table === null || $table === '') return null;
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $table)) throw new InvalidArgumentException('Invalid table name');
        return $table;
    }

    private function sanitizeColumns(string $columns): string
    {
        if ($columns === '*') return '*';
        $parts = array_map('trim', explode(',', $columns));
        foreach ($parts as $part) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?(\s+AS\s+[A-Za-z_][A-Za-z0-9_]*)?$/i', $part)) throw new InvalidArgumentException('Invalid column expression');
        }
        return implode(', ', $parts);
    }

    private function buildFilters(array $filters, array &$params): string
    {
        $where = [];
        $i = 0;
        foreach ($filters as $column => $value) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', (string)$column)) continue;
            $p = ':filter_' . $i++;
            $where[] = $column . ' = ' . $p;
            $params[$p] = $this->normalize($value);
        }
        return implode(' AND ', $where);
    }

    private function normalize($value)
    {
        if (is_bool($value)) return $value ? 1 : 0;
        if (is_array($value) || is_object($value)) return json_encode($value, JSON_UNESCAPED_UNICODE);
        return $value;
    }

    private function hasColumn(string $column): bool
    {
        $stmt = $this->pdo->query('PRAGMA table_info(' . $this->table . ')');
        foreach ($stmt->fetchAll() as $row) if ($row['name'] === $column) return true;
        return false;
    }

    private function ensureColumns(array $columns): void
    {
        foreach ($columns as $column) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $column)) throw new InvalidArgumentException('Invalid column: ' . $column);
            if (!$this->hasColumn($column)) $this->pdo->exec('ALTER TABLE ' . $this->table . ' ADD COLUMN ' . $column . ' TEXT');
        }
    }
}

function supabase(?string $table = null): SupabaseClient
{
    return new SupabaseClient($table);
}
