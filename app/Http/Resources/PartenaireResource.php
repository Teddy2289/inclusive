<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartenaireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'raison_sociale'   => $this->raison_sociale,
            'adresse'          => $this->adresse,
            'cp'               => $this->cp,
            'ville'            => $this->ville,
            'nbrs_salaries'    => $this->nbrs_salaries,
            'secteur_activite' => $this->secteur_activite,
            'telephone_1'      => $this->telephone_1,
            'telephone_2'      => $this->telephone_2,
            'ca'               => $this->ca,
            'siret'            => $this->siret,
            'contacts'         => ContactResource::collection(
                $this->whenLoaded('contacts')
            ),
            'statut' => $this->statut ? [
                'value' => $this->statut->value,
                'label' => $this->statut->label(),
                'color' => $this->statut->color(),
            ] : null,
            'created_at'       => $this->created_at,
        ];
    }
}
