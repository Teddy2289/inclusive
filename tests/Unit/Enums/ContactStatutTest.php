<?php

namespace Tests\Unit\Enums;

use App\Enums\ContactStatut;
use Tests\TestCase;

class ContactStatutTest extends TestCase
{
    public function test_chaque_statut_a_un_label_lisible(): void
    {
        $this->assertSame('À contacter', ContactStatut::A_CONTACTER->label());
        $this->assertSame('RDV planifié', ContactStatut::RDV_PLANIFIE->label());
        $this->assertSame('Converti', ContactStatut::CONVERTI->label());
    }

    public function test_chaque_statut_a_une_couleur(): void
    {
        foreach (ContactStatut::cases() as $statut) {
            $this->assertNotEmpty($statut->color());
        }
    }

    public function test_transition_a_contacter_vers_contacte_autorisee(): void
    {
        $this->assertTrue(
            ContactStatut::A_CONTACTER->peutPasserA(ContactStatut::CONTACTE)
        );
    }

    public function test_transition_a_contacter_vers_converti_non_autorisee(): void
    {
        $this->assertFalse(
            ContactStatut::A_CONTACTER->peutPasserA(ContactStatut::CONVERTI)
        );
    }

    public function test_converti_est_etat_final_sans_transitions(): void
    {
        $this->assertEmpty(ContactStatut::CONVERTI->transitionsAutorisees());
    }

    public function test_sans_suite_peut_relancer_vers_a_contacter(): void
    {
        $this->assertTrue(
            ContactStatut::SANS_SUITE->peutPasserA(ContactStatut::A_CONTACTER)
        );
    }

    public function test_options_retourne_la_bonne_structure(): void
    {
        $options = ContactStatut::options();
        $this->assertCount(count(ContactStatut::cases()), $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }
    }
}
