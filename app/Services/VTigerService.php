<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
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
        $this->url = config('services.vtiger.url');
        $this->username = config('services.vtiger.username');
        $this->accessKey = config('services.vtiger.access_key');
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
            'lastname'         => $data['raison_sociale'],
            'company'          => $data['raison_sociale'],
            'assigned_user_id' => '19x1',

            // ─── Coordonnées ──────────────────────
            'phone'  => preg_replace('/\s+/', '', $data['telephone_1'] ?? ''),
            'mobile' => preg_replace('/\s+/', '', $data['telephone_2'] ?? ''),
            'lane'             => $data['adresse']          ?? '',
            'code'             => $data['cp']               ?? '',
            'city'             => $data['ville']            ?? '',
            'country'          => 'France',

            // ─── Infos entreprise ─────────────────
            'noofemployees' => !empty($data['nbrs_salaries']) ? (int)$data['nbrs_salaries'] : null,

            'annualrevenue' => !empty($data['ca'])            ? (float)$data['ca']          : null,

            'industry'      => $data['secteur_activite'] ?? '',

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
            'lastname'         => $existing['raison_sociale'] ?? 'Inconnu',
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

    // Méthode pour compter le total des comptes (pour la pagination)
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

    public function deleteAllLeads(): int
    {
        // 1. Compter avant suppression
        $count = DB::connection('vtiger')->table('vtiger_leaddetails')->count();

        // 2. Supprimer via SQL direct
        DB::connection('vtiger')->statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::connection('vtiger')->table('vtiger_leadaddress')->delete();
        DB::connection('vtiger')->table('vtiger_leaddetails')->delete();
        DB::connection('vtiger')->table('vtiger_leadscf')->delete();
        DB::connection('vtiger')->table('vtiger_leadsubdetails')->delete();
        DB::connection('vtiger')->table('vtiger_crmentity')->where('setype', 'Leads')->delete();
        DB::connection('vtiger')->statement('SET FOREIGN_KEY_CHECKS = 1');

        // 3. Remettre vtiger_id à null localement
        \App\Models\Partenaire::query()->update(['vtiger_id' => null]);
        \App\Models\Contact::query()->forceDelete();

        return $count;
    }

    public function createContact(array $data, string $leadId): array
    {
        if (!$this->sessionName) $this->login();

        $element = json_encode([
            'lastname'         => $data['conseiller_nom']    ?? 'Inconnu',
            'firstname'        => $data['conseiller_prenom'] ?? '',
            'phone'            => $data['tel']               ?? '',
            'title'            => $data['poste']             ?? '',
            'description'      => $data['commentaires']      ?? '',
            'assigned_user_id' => '19x1',
        ]);

        $response = Http::withoutVerifying()->asForm()->post($this->url, [
            'operation'   => 'create',
            'sessionName' => $this->sessionName,
            'elementType' => 'Contacts',
            'element'     => $element,
        ]);

        $result = $response->json();
        Log::info('vTiger createContact response', (array)($result['result'] ?? $result));

        return $result['result'] ?? [];
    }
}
