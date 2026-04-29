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

        // 1. Chercher si le prospect existe déjà dans vTiger
        $existingId = $vtiger->findLeadByName($this->partenaire->raison_sociale);

        if ($existingId) {
            Log::info('Prospect vTiger existant trouvé : ' . $existingId);
            $this->partenaire->update(['vtiger_id' => $existingId]);
            $leadId = $existingId;
        } else {
            $lead = $vtiger->createLead([
                'raison_sociale'   => $this->partenaire->raison_sociale,
                'adresse'          => $this->partenaire->adresse,
                'cp'               => $this->partenaire->cp,
                'ville'            => $this->partenaire->ville,
                'telephone_1'      => $this->partenaire->telephone_1,
                'telephone_2'      => $this->partenaire->telephone_2,
                'nbrs_salaries'    => $this->partenaire->nbrs_salaries,
                'secteur_activite' => $this->partenaire->secteur_activite,
                'ca'               => $this->partenaire->ca,
            ]);

            if (!isset($lead['id'])) {
                Log::error('Échec création prospect vTiger pour partenaire ' . $this->partenaire->id);
                return;
            }

            $this->partenaire->update(['vtiger_id' => $lead['id']]);
            $leadId = $lead['id'];
        }

        // 2. Sync contacts liés non encore synchronisés
        foreach ($this->partenaire->contacts()->whereNull('vtiger_id')->get() as $contact) {
            $vtContact = $vtiger->createContact([
                'conseiller_nom'    => $contact->conseiller_nom,
                'conseiller_prenom' => $contact->conseiller_prenom,
                'tel'               => $contact->tel,
                'poste'             => $contact->poste,
                'commentaires'      => $contact->commentaires,
            ], $leadId);

            if (isset($vtContact['id'])) {
                $contact->update(['vtiger_id' => $vtContact['id']]);
                Log::info('Contact vTiger créé : ' . $vtContact['id']);
            }
        }
    }
}
