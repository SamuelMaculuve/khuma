<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Companies;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Garantir que a role existe antes de cada teste
        try {
            Role::firstOrCreate(['name' => 'subscriber']);
        } catch (\Exception $e) {
            // Ignorar se não conseguir criar
        }
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '840000000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Verificar autenticação
        $this->assertAuthenticated();
        
        // Verificar redirecionamento
        $response->assertRedirect(route('dashboard', absolute: false));
        
        // Verificar se o usuário foi criado
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'phone' => '840000000',
            'status' => 'pending',
        ]);
        
        // Verificar se a empresa foi criada
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
        ]);
    }

    public function test_registration_requires_company_name(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '840000000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['company_name']);
        $this->assertGuest();
    }

    public function test_registration_requires_phone(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertGuest();
    }

    public function test_registration_requires_unique_email(): void
    {
        // Criar um usuário primeiro
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'company_name' => 'Test Company',
            'email' => 'existing@example.com',
            'phone' => '840000000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}