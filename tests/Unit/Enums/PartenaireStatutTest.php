<?php

namespace Tests\Unit\Enums;

use App\Enums\PartenaireStatut;
use Tests\TestCase;

class PartenaireStatutTest extends TestCase
{
    public function test_chaque_statut_a_un_label_lisible(): void
    {
        $this->assertSame('À prospecter', PartenaireStatut::A_PROSPECTER->label());
        $this->assertSame('En prospection', PartenaireStatut::EN_PROSPECTION->label());
        $this->assertSame('Prospect avec commercial', PartenaireStatut::PROSPECT_COMMERCIAL->label());
        $this->assertSame('Planifié', PartenaireStatut::PLANIFIE->label());
        $this->assertSame('Désactivé', PartenaireStatut::DESACTIVE->label());
    }

    public function test_chaque_statut_a_une_couleur(): void
    {
        foreach (PartenaireStatut::cases() as $statut) {
            $this->assertNotEmpty($statut->color());
        }
    }

    public function test_transition_autorisee_retourne_true(): void
    {
        $this->assertTrue(
            PartenaireStatut::A_PROSPECTER->peutPasserA(PartenaireStatut::EN_PROSPECTION)
        );
    }

    public function test_transition_non_autorisee_retourne_false(): void
    {
        $this->assertFalse(
            PartenaireStatut::A_PROSPECTER->peutPasserA(PartenaireStatut::PLANIFIE)
        );
    }

    public function test_desactive_peut_revenir_a_prospecter(): void
    {
        $this->assertTrue(
            PartenaireStatut::DESACTIVE->peutPasserA(PartenaireStatut::A_PROSPECTER)
        );
    }

    public function test_options_retourne_value_et_label(): void
    {
        $options = PartenaireStatut::options();

        $this->assertCount(count(PartenaireStatut::cases()), $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }
    }
}
