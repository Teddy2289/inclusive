<?php

namespace Tests\Feature\Api;

use App\Enums\ContactStatut;
use App\Models\Contact;
use App\Models\Partenaire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    private function userContact(string ...$permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $user->givePermissionTo($permissions);

        return $user;
    }

    public function test_liste_les_contacts_avec_permission(): void
    {
        $user = $this->userContact('contacts.view');
        Contact::factory()->count(3)->create();

        $this->actingAs($user)
             ->getJson('/api/contacts')
             ->assertStatus(200)
             ->assertJsonCount(3, 'data');
    }

    public function test_filtre_contacts_par_partenaire_id(): void
    {
        $user       = $this->userContact('contacts.view');
        $partenaire = Partenaire::factory()->create();

        Contact::factory()->count(2)->create(['partenaire_id' => $partenaire->id]);
        Contact::factory()->count(3)->create();

        $this->actingAs($user)
             ->getJson("/api/contacts?partenaire_id={$partenaire->id}")
             ->assertStatus(200)
             ->assertJsonCount(2, 'data');
    }

    public function test_cree_un_contact_valide(): void
    {
        $user       = $this->userContact('contacts.create');
        $partenaire = Partenaire::factory()->create();

        $this->actingAs($user)
             ->postJson('/api/contacts', [
                 'partenaire_id'     => $partenaire->id,
                 'conseiller_nom'    => 'Dupont',
                 'conseiller_prenom' => 'Marie',
                 'statut'            => ContactStatut::A_CONTACTER->value,
             ])
             ->assertStatus(201)
             ->assertJsonFragment(['conseiller_nom' => 'Dupont']);

        $this->assertDatabaseHas('contacts', ['conseiller_nom' => 'Dupont']);
    }

    public function test_cree_contact_avec_champs_nullables_vides(): void
    {
        $user       = $this->userContact('contacts.create');
        $partenaire = Partenaire::factory()->create();

        $this->actingAs($user)
             ->postJson('/api/contacts', ['partenaire_id' => $partenaire->id])
             ->assertStatus(201);
    }

    public function test_echoue_si_partenaire_id_inexistant(): void
    {
        $user = $this->userContact('contacts.create');

        $this->actingAs($user)
             ->postJson('/api/contacts', ['partenaire_id' => 9999])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['partenaire_id']);
    }

    public function test_echoue_si_heure_rdv_mauvais_format(): void
    {
        $user       = $this->userContact('contacts.create');
        $partenaire = Partenaire::factory()->create();

        $this->actingAs($user)
             ->postJson('/api/contacts', [
                 'partenaire_id' => $partenaire->id,
                 'heure_rdv'     => '25:99',
             ])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['heure_rdv']);
    }

    public function test_affiche_un_contact_avec_partenaire(): void
    {
        $user    = $this->userContact('contacts.view');
        $contact = Contact::factory()->create();

        $this->actingAs($user)
             ->getJson("/api/contacts/{$contact->id}")
             ->assertStatus(200)
             ->assertJsonStructure([
                 'data' => ['id', 'statut', 'partenaire'],
             ]);
    }

    public function test_met_a_jour_un_contact(): void
    {
        $user    = $this->userContact('contacts.edit');
        $contact = Contact::factory()->create();

        $this->actingAs($user)
             ->putJson("/api/contacts/{$contact->id}", [
                 'fonction' => 'Directeur Commercial',
                 'tel'      => '0601020304',
             ])
             ->assertStatus(200)
             ->assertJsonFragment(['fonction' => 'Directeur Commercial']);
    }

    public function test_supprime_un_contact_soft_delete(): void
    {
        $user    = $this->userContact('contacts.delete');
        $contact = Contact::factory()->create();

        $this->actingAs($user)
             ->deleteJson("/api/contacts/{$contact->id}")
             ->assertStatus(200);

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    public function test_change_statut_contact_valide(): void
    {
        $user    = $this->userContact('contacts.edit');
        $contact = Contact::factory()->create([
            'statut' => ContactStatut::A_CONTACTER,
        ]);

        $this->actingAs($user)
             ->patchJson("/api/contacts/{$contact->id}/statut", [
                 'statut' => ContactStatut::CONTACTE->value,
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.statut.value', ContactStatut::CONTACTE->value);
    }

    public function test_refuse_transition_invalide_contact(): void
    {
        $user    = $this->userContact('contacts.edit');
        $contact = Contact::factory()->create([
            'statut' => ContactStatut::CONVERTI,
        ]);

        $this->actingAs($user)
             ->patchJson("/api/contacts/{$contact->id}/statut", [
                 'statut' => ContactStatut::A_CONTACTER->value,
             ])
             ->assertStatus(422);
    }
}
