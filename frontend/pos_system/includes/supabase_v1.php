<?php
/**
 * Archivo: supabase_v1.php
 * Ubicación: /includes/supabase_v1.php
 * Fecha: 2026-01-07
 * Team MYTS
 */

class SupabaseClient {

    private string $url;
    private string $apiKey;
    private string $table = '';

    public function __construct(string $url, string $apiKey) {
        $this->url = rtrim($url, '/');
        $this->apiKey = $apiKey;
    }

    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }

    private function request(string $method, string $endpoint, ?array $data = null): array {

        $headers = [
            'Content-Type: application/json',
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
            'Prefer: return=representation'
        ];

        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error
            ];
        }

        curl_close($ch);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }

    public function select(array $filters = []): array {
        $query = http_build_query($filters);
        $url = "{$this->url}/rest/v1/{$this->table}" . ($query ? "?{$query}" : '');
        return $this->request('GET', $url);
    }

    public function insert(array $data): array {
        $url = "{$this->url}/rest/v1/{$this->table}";
        return $this->request('POST', $url, $data);
    }

    public function update(array $data, array $where): array {
        $query = http_build_query($where);
        $url = "{$this->url}/rest/v1/{$this->table}?{$query}";
        return $this->request('PATCH', $url, $data);
    }

    public function delete(array $where): array {
        $query = http_build_query($where);
        $url = "{$this->url}/rest/v1/{$this->table}?{$query}";
        return $this->request('DELETE', $url);
    }
}
