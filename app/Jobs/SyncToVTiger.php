<?php

namespace App\Jobs;

use App\Models\Partenaire;
use App\Services\VTigerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncToVTiger implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $partenaire;

    public function __construct(Partenaire $partenaire)
    {
        $this->partenaire = $partenaire;
    }

    public function handle(VTigerService $vtiger)
    {
        Log::info('SyncToVTiger démarré pour partenaire ' . $this->partenaire->id);

        // 1. Chercher si le compte existe déjà dans vTiger
        $existingId = $vtiger->findAccountByName($this->partenaire->raison_sociale);

        if ($existingId) {
            // Compte déjà présent → on sauvegarde juste l'ID
            Log::info('Compte vTiger existant trouvé : ' . $existingId);
            $this->partenaire->update(['vtiger_id' => $existingId]);
            $accountId = $existingId;
        } else {
            // Compte absent → on crée
            $account = $vtiger->createAccount([
                'raison_sociale'   => $this->partenaire->raison_sociale,
                'adresse'          => $this->partenaire->adresse,
                'cp'               => $this->partenaire->cp,
                'ville'            => $this->partenaire->ville,
                'telephone_1'      => $this->partenaire->telephone_1,
                'telephone_2'      => $this->partenaire->telephone_2,
                'siret'            => $this->partenaire->siret,
                'nbrs_salaries'    => $this->partenaire->nbrs_salaries,
                'secteur_activite' => $this->partenaire->secteur_activite,
                'ca'               => $this->partenaire->ca,
            ]);

            Log::info('Réponse vTiger createAccount', (array)$account);

            if (!isset($account['id'])) {
                Log::error('Échec création vTiger pour partenaire ' . $this->partenaire->id);
                return;
            }

            $this->partenaire->update(['vtiger_id' => $account['id']]);
            $accountId = $account['id'];
        }

        // 2. Sync contacts
        foreach ($this->partenaire->contacts()->where('is_synced_vtiger', false)->get() as $contact) {
            $vtContact = $vtiger->createContact([
                'prenom'       => $contact->conseiller_prenom,
                'nom'          => $contact->conseiller_nom,
                'telephone'    => $this->partenaire->telephone_1,
                'commentaires' => $contact->commentaires,
            ], $accountId);

            if (isset($vtContact['id'])) {
                $contact->update(['is_synced_vtiger' => true]);
            }
        }
    }
}
