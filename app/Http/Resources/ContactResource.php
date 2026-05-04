<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'conseiller_nom'       => $this->conseiller_nom,
            'conseiller_prenom'    => $this->conseiller_prenom,
            'conseiller'           => $this->conseiller_full_name,
            'statut'               => [
                'value' => $this->statut?->value,
                'label' => $this->statut?->label(),
                'color' => $this->statut?->color(),
            ],
            'date_premier_contact' => $this->date_premier_contact?->format('d/m/Y'),
            'commentaires'         => $this->commentaires,
            'poste'                => $this->poste,
            'tel'                  => $this->tel,
            'fonction'             => $this->fonction,
            'date_rdv'             => $this->date_rdv?->format('d/m/Y'),
            'heure_rdv'            => $this->heure_rdv
                ? \Carbon\Carbon::parse($this->heure_rdv)->format('H:i')
                : null,

            // ✅ many-to-many → collection de partenaires
            'partenaires'          => PartenaireResource::collection(
                $this->whenLoaded('partenaires')
            ),
            'created_at'           => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
