<?php

namespace App\Http\Requests;

use App\Enums\ContactStatut;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'conseiller_nom'       => 'nullable|string|max:100',
            'conseiller_prenom'    => 'nullable|string|max:100',
            'statut'               => ['nullable', new Enum(ContactStatut::class)],
            'date_premier_contact' => 'nullable|date',
            'commentaires'         => 'nullable|string',
            'poste'                => 'nullable|string|max:100',
            'tel'                  => 'nullable|string|max:20',
            'fonction'             => 'nullable|string|max:150',
            'date_rdv'             => 'nullable|date',
            'heure_rdv'            => 'nullable|date_format:H:i',
        ];
    }
}
