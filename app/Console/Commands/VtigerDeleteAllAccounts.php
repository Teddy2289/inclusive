<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class VtigerDeleteAllAccounts extends Command
{
    protected $signature   = 'vtiger:delete-all-accounts';
    protected $description = 'Supprime tous les comptes dans vTiger CRM';

    public function handle()
    {
        $url       = 'https://crm.allopro24.com/webservice.php';
        $username  = 'admin';
        $accessKey = 'jjwbgscPe3qmW4oq';

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

        // 2. Récupérer tous les IDs par batch de 100
        $offset  = 0;
        $deleted = 0;

        do {
            $response = Http::withoutVerifying()->get($url, [
                'operation'   => 'query',
                'sessionName' => $session,
                'query'       => "SELECT id FROM Accounts LIMIT $offset, 100;",
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

        $this->info("$deleted comptes supprimés. vtiger_id remis à null.");
    }
}
