<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_avec_identifiants_valides(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                     'user' => ['id', 'name', 'email', 'roles', 'permissions'],
                 ]);
    }

    public function test_login_echoue_avec_mauvais_mot_de_passe(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'mauvais_mdp',
        ])->assertStatus(401)
          ->assertJson(['message' => 'Identifiants incorrects.']);
    }

    public function test_register_cree_un_utilisateur(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token', 'user']);

        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
    }

    public function test_register_echoue_si_email_deja_utilise(): void
    {
        User::factory()->create(['email' => 'jean@example.com']);

        $this->postJson('/api/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['email']);
    }

    public function test_logout_revoque_le_token(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
             ->postJson('/api/logout')
             ->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_me_retourne_le_profil_connecte(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
             ->getJson('/api/me')
             ->assertStatus(200)
             ->assertJsonFragment(['email' => $user->email]);
    }
}
