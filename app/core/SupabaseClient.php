<?php

class SupabaseClient
{
    private string $url;
    private string $anonKey;
    private ?string $jwt;

    public function __construct(?string $jwt = null)
    {
        $this->url = SUPABASE_URL;
        $this->anonKey = SUPABASE_ANON_KEY;
        $this->jwt = $jwt;
    }

    private function headers(): array
    {
        $headers = [
            "apikey: {$this->anonKey}",
            "Content-Type: application/json"
        ];

        if ($this->jwt) {
            $headers[] = "Authorization: Bearer {$this->jwt}";
        }

        return $headers;
    }

    public function get(string $table, string $query = '')
    {
        $url = "{$this->url}/rest/v1/{$table}{$query}";
        return $this->request('GET', $url);
    }

    public function post(string $table, array $data)
    {
        $url = "{$this->url}/rest/v1/{$table}";
        return $this->request('POST', $url, $data);
    }

    public function patch(string $table, string $filter, array $data)
    {
        $url = "{$this->url}/rest/v1/{$table}?{$filter}";
        return $this->request('PATCH', $url, $data);
    }

    public function delete(string $table, string $filter)
    {
        $url = "{$this->url}/rest/v1/{$table}?{$filter}";
        return $this->request('DELETE', $url);
    }

    /**
     * Get total row count for a table (optional filter).
     * Uses Prefer: count=exact and returns integer.
     */
    public function count(string $table, string $query = ''): int
    {
        $headers = array_merge($this->headers(), ['Prefer: count=exact']);
        $url = "{$this->url}/rest/v1/{$table}{$query}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HEADER         => true
        ]);
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $headerStr = substr($response, 0, $headerSize);
        if (preg_match('/Content-Range: \d+-\d+\/(\d+)/', $headerStr, $m)) {
            return (int) $m[1];
        }
        return 0;
    }

    private function request(string $method, string $url, array $data = null)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $this->headers()
        ]);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$status, json_decode($response, true)];
    }
}
