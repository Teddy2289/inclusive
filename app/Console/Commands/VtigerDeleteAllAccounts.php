<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class VtigerDeleteAllAccounts extends Command
{
    protected $signature   = 'vtiger:delete-all-leads';
    protected $description = 'Supprime tous les Prospects (Leads) dans vTiger CRM';

    public function handle()
    {
        $url       = config('services.vtiger.url');
        $username  = config('services.vtiger.username');
        $accessKey = config('services.vtiger.access_key');

        // 1. Login
        $token = Http::withoutVerifying()
            ->get($url, ['operation' => 'getchallenge', 'username' => $username])
            ->json('result.token');

        $hash = md5($token . $accessKey);

        $session = Http::withoutVerifying()->asForm()->post($url, [
            'operation' => 'login',
            'username'  => $username,
            'accessKey' => $hash,
        ])->json('result.sessionName');

        $this->info("Session: $session");

        // 2. Récupérer et supprimer tous les Leads par batch de 100
        $offset  = 0;
        $deleted = 0;

        do {
            $response = Http::withoutVerifying()->get($url, [
                'operation'   => 'query',
                'sessionName' => $session,
                'query'       => "SELECT id FROM Leads LIMIT $offset, 100;",
            ]);

            $records = $response->json('result') ?? [];

            if (empty($records)) break;

            foreach ($records as $record) {
                $deleteResponse = Http::withoutVerifying()->asForm()->post($url, [
                    'operation'   => 'delete',
                    'sessionName' => $session,
                    'id'          => $record['id'],
                ]);

                $success = $deleteResponse->json('success');
                $deleted++;
                $this->line("Supprimé: {$record['id']} — " . ($success ? '✅' : '❌'));
            }

            $offset += 100;

        } while (count($records) === 100);

        // 3. Remettre vtiger_id à null en base locale
        \App\Models\Partenaire::query()->update(['vtiger_id' => null]);

        $this->info("$deleted prospects supprimés. vtiger_id remis à null.");
    }
}
