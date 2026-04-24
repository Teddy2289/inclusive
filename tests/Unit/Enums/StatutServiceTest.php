<?php

namespace Tests\Unit\Services;

use App\Enums\ContactStatut;
use App\Enums\PartenaireStatut;
use App\Models\Contact;
use App\Models\Partenaire;
use App\Services\StatutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StatutServiceTest extends TestCase
{
    use RefreshDatabase;

    private StatutService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StatutService();
    }

    public function test_changer_statut_partenaire_transition_valide(): void
    {
        $partenaire = Partenaire::factory()->create([
            'statut' => PartenaireStatut::A_PROSPECTER,
        ]);

        $result = $this->service->changerStatutPartenaire(
            $partenaire,
            PartenaireStatut::EN_PROSPECTION->value
        );

        $this->assertSame(PartenaireStatut::EN_PROSPECTION, $result->statut);
    }

    public function test_changer_statut_partenaire_transition_invalide(): void
    {
        $this->expectException(ValidationException::class);

        $partenaire = Partenaire::factory()->create([
            'statut' => PartenaireStatut::A_PROSPECTER,
        ]);

        $this->service->changerStatutPartenaire(
            $partenaire,
            PartenaireStatut::PLANIFIE->value
        );
    }

    public function test_transitions_partenaire_retourne_bonne_structure(): void
    {
        $partenaire = Partenaire::factory()->create([
            'statut' => PartenaireStatut::EN_PROSPECTION,
        ]);

        $transitions = $this->service->transitionsPartenaire($partenaire);

        $this->assertNotEmpty($transitions);

        foreach ($transitions as $t) {
            $this->assertArrayHasKey('value', $t);
            $this->assertArrayHasKey('label', $t);
            $this->assertArrayHasKey('color', $t);
        }
    }

    public function test_changer_statut_contact_transition_valide(): void
    {
        $contact = Contact::factory()->create([
            'statut' => ContactStatut::A_CONTACTER,
        ]);

        $result = $this->service->changerStatutContact(
            $contact,
            ContactStatut::CONTACTE->value
        );

        $this->assertSame(ContactStatut::CONTACTE, $result->statut);
    }

    public function test_changer_statut_contact_transition_invalide(): void
    {
        $this->expectException(ValidationException::class);

        $contact = Contact::factory()->create([
            'statut' => ContactStatut::A_CONTACTER,
        ]);

        $this->service->changerStatutContact(
            $contact,
            ContactStatut::CONVERTI->value
        );
    }

    public function test_transitions_contact_retourne_bonne_structure(): void
    {
        $contact = Contact::factory()->create([
            'statut' => ContactStatut::CONTACTE,
        ]);

        $transitions = $this->service->transitionsContact($contact);

        $this->assertNotEmpty($transitions);

        foreach ($transitions as $t) {
            $this->assertArrayHasKey('value', $t);
            $this->assertArrayHasKey('label', $t);
            $this->assertArrayHasKey('color', $t);
        }
    }
}
