<?php

namespace App\Http\Requests;

use App\Enums\PartenaireStatut;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartenaireRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'raison_sociale'   => 'required|string|max:255',
            'adresse'          => 'nullable|string|max:255',
            'cp'               => 'nullable|string|max:10',
            'ville'            => 'nullable|string|max:100',
            'nbrs_salaries'    => 'nullable|integer|min:0',
            'secteur_activite' => 'nullable|string|max:255',
            'telephone_1'      => 'nullable|string|max:20',
            'telephone_2'      => 'nullable|string|max:20',
            'ca'               => 'nullable|numeric|min:0',
            'siret'            => 'nullable|string|max:20|unique:partenaires,siret',
            'statut' => [
                'nullable',
                Rule::enum(PartenaireStatut::class)
            ],
        ];
    }
}
