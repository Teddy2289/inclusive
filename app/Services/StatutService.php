<?php

namespace App\Services;

use App\Enums\ContactStatut;
use App\Enums\PartenaireStatut;
use App\Models\Contact;
use App\Models\Partenaire;
use Illuminate\Validation\ValidationException;

class StatutService
{
    public function changerStatutPartenaire(Partenaire $partenaire, string $nouveauStatut): Partenaire
    {
        $nouveau = PartenaireStatut::from($nouveauStatut);

        if (! $partenaire->statut->peutPasserA($nouveau)) {
            throw ValidationException::withMessages([
                'statut' => "Transition de «{$partenaire->statut->label()}» vers «{$nouveau->label()}» non autorisée.",
            ]);
        }

        $partenaire->update(['statut' => $nouveau]);

        return $partenaire->fresh();
    }

    public function changerStatutContact(Contact $contact, string $nouveauStatut): Contact
    {
        $nouveau = ContactStatut::from($nouveauStatut);

        if (! $contact->statut->peutPasserA($nouveau)) {
            throw ValidationException::withMessages([
                'statut' => "Transition de «{$contact->statut->label()}» vers «{$nouveau->label()}» non autorisée.",
            ]);
        }

        $contact->update(['statut' => $nouveau]);

        return $contact->fresh();
    }

    public function transitionsPartenaire(Partenaire $partenaire): array
    {
        return array_map(
            fn(PartenaireStatut $s) => [
                'value' => $s->value,
                'label' => $s->label(),
                'color' => $s->color(),
            ],
            $partenaire->statut->transitionsAutorisees()
        );
    }

    public function transitionsContact(Contact $contact): array
    {
        return array_map(
            fn(ContactStatut $s) => [
                'value' => $s->value,
                'label' => $s->label(),
                'color' => $s->color(),
            ],
            $contact->statut->transitionsAutorisees()
        );
    }


}
