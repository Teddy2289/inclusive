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

        // Calcul SIREN et Code Client depuis le SIRET
        $siret      = preg_replace('/[^0-9]/', '', $data['siret'] ?? '');
        $siren      = strlen($siret) >= 9 ? substr($siret, 0, 9) : null;
        $cp         = preg_replace('/\s+/', '', $data['cp'] ?? '');
        $codeClient = ($siren && $cp) ? $siren . $cp : null;

        $element = json_encode([
            // ─── Champs Standards ───────────────────────────────────────
            'lastname'         => $data['raison_sociale'],
            'firstname'        => $data['nom_alternatif'] ?? '',            // Nom alternatif
            'company'          => $data['raison_sociale'],
            'assigned_user_id' => '19x1',
            'phone'            => preg_replace('/\s+/', '', $data['telephone_1'] ?? ''),
            'mobile'           => preg_replace('/\s+/', '', $data['telephone_2'] ?? ''),
            'lane'             => $data['adresse'] ?? '',
            'code'             => $cp,
            'city'             => $data['ville'] ?? '',
            'country'          => 'France',
            'noofemployees'    => !empty($data['nbrs_salaries']) ? (int)$data['nbrs_salaries'] : null,
            'annualrevenue'    => !empty($data['ca']) ? (float)$data['ca'] : null,
            'industry'         => $data['secteur_activite'] ?? '',
            'leadstatus'       => 'A contacter',
            'leadsource'       => 'Auto Généré',

            // ─── Champs Personnalisés ────────────────────────────────────
            'cf_code_client'        => $codeClient,                        // SIREN + CP
            'cf_type_tiers'         => $data['type_tiers']        ?? 'Particulier',
            'cf_type_entite_legale' => $data['forme_juridique']   ?? '',
            'cf_nature_tiers'       => $data['nature_tiers']      ?? 'Client',
            'cf_potentiel_prospect' => $data['potentiel']         ?? '',
            'cf_statut_prospection' => $data['statut_prospection'] ?? 'Nouveau',
            'cf_origine_contact'    => $data['origine']           ?? 'Fichier Excel',
            'cf_statut_sirene'      => $data['statut_sirene']     ?? 'Actif',
            'cf_nom_alternatif'     => $data['nom_alternatif']    ?? '',
            'cf_region'             => $data['region']            ?? '',
            'cf_commerciaux'        => $data['commerciaux']       ?? '',
            'cf_interlocuteur'      => $data['interlocuteur']     ?? '',
            'cf_vendu_par'          => $data['vendu_par']         ?? '',
            'cf_maison_mere'        => $data['maison_mere']       ?? '',
            'cf_salarie_de'         => $data['salarie_de']        ?? '',
            'cf_suivi_client'       => $data['suivi_client']      ?? '',
            'cf_a_recontacter'      => $data['a_recontacter']     ?? '0',
            'cf_partenaire_boutique' => $data['partenaire_boutique'] ?? '0',
            'cf_partenaire_like'    => $data['partenaire_like']   ?? '0',
            'cf_convention_signee'  => $data['convention_signee'] ?? '0',
            'cf_rgpd'               => $data['rgpd']              ?? '0',
            'cf_ne_plus_contacter'  => $data['ne_plus_contacter'] ?? '0',
            'cf_rqth'               => $data['rqth']              ?? '0',
            'cf_opco'               => $data['opco']              ?? '',
            'cf_inscrit_ft'         => $data['inscrit_ft']        ?? '0',
            'cf_parrainage'         => $data['parrainage']        ?? '',
            'cf_montant_cpf'        => !empty($data['montant_cpf']) ? (float)$data['montant_cpf'] : 0,
            'cf_commentaires'       => $data['commentaires']      ?? '',
            'cf_maj_sirene'         => '0',
            'cf_statut_sirene'      => $data['statut_sirene']     ?? 'Actif',
            'cf_id_import'          => $data['id_import']         ?? '',
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
        $whereClause = !empty($search) ? "WHERE accountname LIKE '%" . addslashes($search) . "%'" : "";

        // Ajout des cf_ dans la requête SELECT si tu transformes tes Leads en Comptes plus tard
        $query = "SELECT id, accountname, phone, bill_city,
                         cf_type_tiers, cf_statut_prospection
                  FROM Accounts
                  {$whereClause}
                  ORDER BY accountname ASC
                  LIMIT {$offset}, {$limit};";

        $response = Http::withoutVerifying()->get($this->url, [
            'operation'   => 'query',
            'sessionName' => $this->sessionName,
            'query'       => $query,
        ]);

        return $response->json('result') ?? [];
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
        DB::table('vtiger_leadscf')->delete();
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
