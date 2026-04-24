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

    public function createAccount(array $data)
    {
        if (!$this->sessionName) $this->login();

        $element = json_encode([
            'accountname'      => $data['raison_sociale'],
            'phone'            => $data['telephone_1'] ?? '',
            'otherphone'       => $data['telephone_2'] ?? '',
            'employees'        => $data['nbrs_salaries'] ?? '',
            'annual_revenue'   => $data['ca'] ?? '',
            'siccode'          => $data['siret'] ?? '',
            'bill_street'      => $data['adresse'] ?? '',
            'bill_code'        => $data['cp'] ?? '',
            'bill_city'        => $data['ville'] ?? '',
            'bill_country'     => 'France',
            'accounttype'      => 'Prospect',
            'industry'      => $data['secteur_activite'] ?? '',
            'assigned_user_id' => '19x1',
        ]);

        $response = Http::withoutVerifying()->asForm()->post($this->url, [
            'operation'   => 'create',
            'sessionName' => $this->sessionName,
            'elementType' => 'Accounts',
            'element'     => $element,
        ]);

        return $response->json('result');
    }

    public function createContact(array $data, string $accountId = null)
    {
        if (!$this->sessionName) $this->login();

        $elementData = [
            'firstname'        => $data['prenom'] ?? '',
            'lastname'         => $data['nom'] ?: 'CLIENT',
            'phone'            => $data['telephone'] ?? '',
            'description'      => $data['commentaires'] ?? '',
            'assigned_user_id' => '19x1'
        ];

        if ($accountId) {
            $elementData['account_id'] = $accountId;
        }

        // Et ici
        $response = Http::withoutVerifying()->asForm()->post($this->url, [
            'operation'   => 'create',
            'sessionName' => $this->sessionName,
            'elementType' => 'Contacts',
            'element'     => json_encode($elementData)
        ]);

        return $response->json('result');
    }

    public function deactivateAccount(string $vtigerId): bool
    {
        if (!$this->sessionName) $this->login();

        // D'abord récupérer le compte pour avoir l'accountname obligatoire
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
            'id'          => $vtigerId,
            'accountname' => $existing['accountname'], // ← obligatoire
            'accounttype' => 'Competitor',
            'rating'      => 'Shutdown',
            'assigned_user_id' => $existing['assigned_user_id'],
        ]);

        $response = Http::withoutVerifying()->asForm()->post($this->url, [
            'operation'   => 'update',
            'sessionName' => $this->sessionName,
            'elementType' => 'Accounts',
            'element'     => $element,
        ]);

        $result = $response->json();
        Log::info('vTiger deactivateAccount response', $result ?? []);

        return $result['success'] ?? false;
    }

    public function findAccountByName(string $name): ?string
    {
        if (!$this->sessionName) $this->login();

        $query = urlencode("SELECT id FROM Accounts WHERE accountname = '" . addslashes($name) . "' LIMIT 1;");

        $response = Http::withoutVerifying()->get($this->url, [
            'operation'   => 'query',
            'sessionName' => $this->sessionName,
            'query'       => "SELECT id FROM Accounts WHERE accountname = '" . addslashes($name) . "' LIMIT 1;",
        ]);

        $records = $response->json('result');

        return isset($records[0]['id']) ? $records[0]['id'] : null;
    }
}
