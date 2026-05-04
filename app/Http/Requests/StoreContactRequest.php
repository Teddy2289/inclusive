<?php

namespace App\Http\Requests;

use App\Enums\ContactStatut;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'partenaire_ids.*'     => 'exists:partenaires,id',
            'conseiller_nom'       => 'nullable|string|max:100',
            'conseiller_prenom'    => 'nullable|string|max:100',
            'statut'               => ['nullable', new Enum(ContactStatut::class)],
            'date_premier_contact' => 'nullable|date',
            'commentaires'         => 'nullable|string',
            'poste'                => 'nullable|string|max:100',
            'tel'                  => 'nullable|string|max:20',
            'fonction'             => 'nullable|string|max:150',
            'date_rdv'             => 'nullable|date|after_or_equal:today',
            'heure_rdv'            => 'nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'partenaire_id.exists' => 'Le partenaire sélectionné n\'existe pas.',
            'date_rdv.after_or_equal' => 'La date de RDV ne peut pas être dans le passé.',
            'heure_rdv.date_format'   => 'L\'heure doit être au format HH:MM.',
        ];
    }
}
