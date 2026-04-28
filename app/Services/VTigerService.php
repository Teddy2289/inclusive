<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VTigerService
{
    protected $url;
    protected $username;
    protected $accessKey;
    protected $sessionName;

    public function __construct()
    {
        $this->url = env('CRM_URL');
        $this->username = env('CRM_USERNAME');
        $this->accessKey = env('CRM_ACCESS_KEY');
    }
    protected function login()
    {

        $response = Http::withoutVerifying()->get($this->url, [
            'operation' => 'getchallenge',
            'username' => $this->username
        ]);

        $token = $response->json('result.token');
        $accessKeyHash = md5($token . $this->accessKey);


        $loginResponse = Http::withoutVerifying()->asForm()->post($this->url, [
            'operation' => 'login',
            'username' => $this->username,
            'accessKey' => $accessKeyHash
        ]);

        $this->sessionName = $loginResponse->json('result.sessionName');
    }

    public function createLead(array $data): array
    {
        if (!$this->sessionName) $this->login();

        $element = json_encode([
            // ─── Champs obligatoires ───────────────
            'lastname'         => $data['raison_sociale'],   // Nom (obligatoire)
            'company'          => $data['raison_sociale'],   // Société
            'assigned_user_id' => '19x1',

            // ─── Coordonnées ──────────────────────
            'phone'            => $data['telephone_1']      ?? '',
            'mobile'           => $data['telephone_2']      ?? '',
            'lane'             => $data['adresse']          ?? '',
            'code'             => $data['cp']               ?? '',
            'city'             => $data['ville']            ?? '',
            'country'          => 'France',

            // ─── Infos entreprise ─────────────────
            'noofemployees'    => $data['nbrs_salaries']    ?? '',
            'annualrevenue'    => $data['ca']               ?? '',
            'description'      => $data['secteur_activite'] ?? '',

            // ─── Statut par défaut ─────────────────
            'leadstatus'       => 'A contacter',
            'leadsource'       => 'Self Generated',
        ]);

        $response = Http::withoutVerifying()->asForm()->post($this->url, [
            'operation'   => 'create',
            'sessionName' => $this->sessionName,
            'elementType' => 'Leads',
            'element'     => $element,
        ]);

        $result = $response->json();
        Log::info('vTiger createLead response', (array)($result['result'] ?? $result));

        return $result['result'] ?? [];
    }

    public function findLeadByName(string $name): ?string
    {
        if (!$this->sessionName) $this->login();

        $response = Http::withoutVerifying()->get($this->url, [
            'operation'   => 'query',
            'sessionName' => $this->sessionName,
            'query'       => "SELECT id FROM Leads WHERE company = '" . addslashes($name) . "' LIMIT 1;",
        ]);

        $records = $response->json('result');
        return isset($records[0]['id']) ? $records[0]['id'] : null;
    }

    public function deactivateLead(string $vtigerId): bool
    {
        if (!$this->sessionName) $this->login();

        $existing = Http::withoutVerifying()->get($this->url, [
            'operation'   => 'retrieve',
            'sessionName' => $this->sessionName,
            'id'          => $vtigerId,
        ])->json('result');

        if (!$existing) {
            Log::warning('vTiger retrieve failed pour ' . $vtigerId);
            return false;
        }

        $element = json_encode([
            'id'               => $vtigerId,
            'lastname'         => $existing['lastname'],
            'leadstatus'       => 'Hors Cible - Refus',   // ← statut désactivé
            'rating'           => 'Shutdown',
            'assigned_user_id' => $existing['assigned_user_id'],
        ]);

        $response = Http::withoutVerifying()->asForm()->post($this->url, [
            'operation'   => 'update',
            'sessionName' => $this->sessionName,
            'elementType' => 'Leads',
            'element'     => $element,
        ]);

        $result = $response->json();
        Log::info('vTiger deactivateLead response', $result ?? []);

        return $result['success'] ?? false;
    }

    public function getAccounts(int $page = 1, int $limit = 20, string $search = ''): array
    {
        if (!$this->sessionName) $this->login();

        $offset = ($page - 1) * $limit;

        $whereClause = '';
        if (!empty($search)) {
            $search = addslashes($search);
            $whereClause = "WHERE accountname LIKE '%{$search}%'";
        }

        $query = "SELECT id, accountname, phone, bill_city, bill_code,
                     industry, employees, annual_revenue, accounttype,
                     statut_prospect, siccode
              FROM Accounts
              {$whereClause}
              ORDER BY accountname ASC
              LIMIT {$offset}, {$limit};";

        $response = Http::withoutVerifying()->get($this->url, [
            'operation'   => 'query',
            'sessionName' => $this->sessionName,
            'query'       => $query,
        ]);

        $result = $response->json();
        Log::info('vTiger getAccounts', ['success' => $result['success'] ?? false]);

        return $result['result'] ?? [];
    }

    public function countAccounts(string $search = ''): int
    {
        if (!$this->sessionName) $this->login();

        $whereClause = '';
        if (!empty($search)) {
            $search = addslashes($search);
            $whereClause = "WHERE accountname LIKE '%{$search}%'";
        }

        $query = "SELECT COUNT(*) as total FROM Accounts {$whereClause};";

        $response = Http::withoutVerifying()->get($this->url, [
            'operation'   => 'query',
            'sessionName' => $this->sessionName,
            'query'       => $query,
        ]);

        return $response->json('result.0.count') ?? 0;
    }
}
