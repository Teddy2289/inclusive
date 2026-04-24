<?php

namespace Tests\Feature\Api;

use App\Enums\PartenaireStatut;
use App\Models\Partenaire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PartenaireTest extends TestCase
{
    use RefreshDatabase;

    private function userAvecPermission(string ...$permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $user->givePermissionTo($permissions);

        return $user;
    }

    public function test_liste_les_partenaires_avec_permission(): void
    {
        $user = $this->userAvecPermission('partenaires.view');
        Partenaire::factory()->count(3)->create();

        $this->actingAs($user)
             ->getJson('/api/partenaires')
             ->assertStatus(200)
             ->assertJsonCount(3, 'data');
    }

    public function test_refuse_la_liste_sans_permission(): void
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */

        $this->actingAs($user)
             ->getJson('/api/partenaires')
             ->assertStatus(403);
    }

    public function test_liste_necessite_authentification(): void
    {
        $this->getJson('/api/partenaires')
             ->assertStatus(401);
    }

    public function test_cree_un_partenaire_valide(): void
    {
        $user = $this->userAvecPermission('partenaires.create');

        $this->actingAs($user)
             ->postJson('/api/partenaires', [
                 'raison_sociale' => 'Acme Corp',
                 'ville'          => 'Paris',
                 'cp'             => '75001',
                 'siret'          => '12345678900001',
             ])
             ->assertStatus(201)
             ->assertJsonFragment(['raison_sociale' => 'Acme Corp']);

        $this->assertDatabaseHas('partenaires', ['raison_sociale' => 'Acme Corp']);
    }

    public function test_echoue_si_raison_sociale_manquante(): void
    {
        $user = $this->userAvecPermission('partenaires.create');

        $this->actingAs($user)
             ->postJson('/api/partenaires', ['ville' => 'Paris'])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['raison_sociale']);
    }

    public function test_affiche_un_partenaire_existant(): void
    {
        $user       = $this->userAvecPermission('partenaires.view');
        $partenaire = Partenaire::factory()->create();

        $this->actingAs($user)
             ->getJson("/api/partenaires/{$partenaire->id}")
             ->assertStatus(200)
             ->assertJsonFragment(['id' => $partenaire->id]);
    }

    public function test_retourne_404_pour_partenaire_inexistant(): void
    {
        $user = $this->userAvecPermission('partenaires.view');

        $this->actingAs($user)
             ->getJson('/api/partenaires/9999')
             ->assertStatus(404);
    }

    public function test_met_a_jour_un_partenaire(): void
    {
        $user       = $this->userAvecPermission('partenaires.edit');
        $partenaire = Partenaire::factory()->create();

        $this->actingAs($user)
             ->putJson("/api/partenaires/{$partenaire->id}", [
                 'raison_sociale' => 'Nouveau Nom',
             ])
             ->assertStatus(200)
             ->assertJsonFragment(['raison_sociale' => 'Nouveau Nom']);
    }

    public function test_supprime_un_partenaire(): void
    {
        $user       = $this->userAvecPermission('partenaires.delete');
        $partenaire = Partenaire::factory()->create();

        $this->actingAs($user)
             ->deleteJson("/api/partenaires/{$partenaire->id}")
             ->assertStatus(200);

        $this->assertSoftDeleted('partenaires', ['id' => $partenaire->id]);
    }

    public function test_change_statut_transition_valide(): void
    {
        $user       = $this->userAvecPermission('partenaires.edit');
        $partenaire = Partenaire::factory()->create([
            'statut' => PartenaireStatut::A_PROSPECTER,
        ]);

        $this->actingAs($user)
             ->patchJson("/api/partenaires/{$partenaire->id}/statut", [
                 'statut' => PartenaireStatut::EN_PROSPECTION->value,
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.statut.value', PartenaireStatut::EN_PROSPECTION->value);
    }

    public function test_refuse_transition_statut_invalide(): void
    {
        $user       = $this->userAvecPermission('partenaires.edit');
        $partenaire = Partenaire::factory()->create([
            'statut' => PartenaireStatut::A_PROSPECTER,
        ]);

        $this->actingAs($user)
             ->patchJson("/api/partenaires/{$partenaire->id}/statut", [
                 'statut' => PartenaireStatut::PLANIFIE->value,
             ])
             ->assertStatus(422);
    }
}
