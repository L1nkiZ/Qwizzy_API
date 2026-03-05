<?php

namespace Tests\Feature;

use App\Http\Helpers\TokenHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Register ─────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_register_a_new_user()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'NouvelUtilisateur',
            'email'    => 'nouveau@example.com',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'error'   => false,
                'message' => 'Utilisateur créé avec succès',
            ])
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role_id']]);

        $this->assertDatabaseHas('users', ['email' => 'nouveau@example.com']);
    }

    #[Test]
    public function it_assigns_role_membre_by_default_on_register()
    {
        $this->postJson('/api/auth/register', [
            'username' => 'UserDefaut',
            'email'    => 'defaut@example.com',
            'password' => 'motdepasse123',
        ]);

        $this->assertDatabaseHas('users', [
            'email'   => 'defaut@example.com',
            'role_id' => 1,
        ]);
    }

    #[Test]
    public function it_can_register_with_custom_role()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'Redacteur',
            'email'    => 'redacteur@example.com',
            'password' => 'motdepasse123',
            'role_id'  => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson(['error' => false]);

        $this->assertDatabaseHas('users', [
            'email'   => 'redacteur@example.com',
            'role_id' => 2,
        ]);
    }

    #[Test]
    public function it_validates_username_is_required_on_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'email'    => 'test@example.com',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['error' => true]);
    }

    #[Test]
    public function it_validates_email_is_required_on_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'SansEmail',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['error' => true]);
    }

    #[Test]
    public function it_validates_password_is_required_on_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'SansMotDePasse',
            'email'    => 'sansmotdepasse@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['error' => true]);
    }

    #[Test]
    public function it_validates_password_minimum_length_on_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'MotDePaseCourt',
            'email'    => 'court@example.com',
            'password' => 'court',
        ]);

        $response->assertStatus(200)
            ->assertJson(['error' => true]);
    }

    #[Test]
    public function it_prevents_duplicate_email_on_register()
    {
        // Le seeder crée déjà un user member@example.com
        $response = $this->postJson('/api/auth/register', [
            'username' => 'Doublon',
            'email'    => 'member@example.com',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['error' => true]);
    }

    #[Test]
    public function it_validates_email_format_on_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'username' => 'MailInvalide',
            'email'    => 'pasunemail',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['error' => true]);
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_login_with_valid_credentials()
    {
        // The migration seeds 3 default users with password 'password'
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'member@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'error'      => false,
                'message'    => 'Authentification réussie',
                'token_type' => 'Bearer',
            ])
            ->assertJsonStructure(['token', 'user', 'expires_at']);
    }

    #[Test]
    public function it_returns_error_with_wrong_password()
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'member@example.com',
            'password' => 'mauvais_mot_de_passe',
        ]);

        $response->assertStatus(500)
            ->assertJson(['error' => true]);
    }

    #[Test]
    public function it_returns_error_with_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'inexistant@example.com',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(500)
            ->assertJson(['error' => true]);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_get_authenticated_user_with_valid_token()
    {
        $token = TokenHelper::createToken(User::find(1));

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
            ])
            ->assertJsonPath('user.email', 'member@example.com');
    }

    #[Test]
    public function it_returns_error_on_me_without_token()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson(['error' => true]);
    }

    #[Test]
    public function it_can_logout_with_valid_token()
    {
        // The migration seeds a user with id=3 (admin), which TokenHelper uses
        $token = TokenHelper::createToken(User::find(3));

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'error'   => false,
                'message' => 'Déconnexion réussie. Veuillez supprimer le token côté client.',
            ]);
    }

    #[Test]
    public function it_returns_error_on_logout_without_token()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401)
            ->assertJson(['error' => true]);
    }
}
