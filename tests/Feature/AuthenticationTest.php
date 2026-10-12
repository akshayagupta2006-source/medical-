<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement 6.1: Valid login succeeds (Web & API).
     */
    public function test_valid_web_login_succeeds(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@jajalmedical.com',
            'password' => Hash::make('Password123!'),
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@jajalmedical.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_valid_api_login_succeeds_and_returns_sanctum_token(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@jajalmedical.com',
            'password' => Hash::make('Password123!'),
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@jajalmedical.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Authenticated successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);
    }

    /**
     * Requirement 6.2: Invalid login is rejected (Web & API).
     */
    public function test_invalid_web_login_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'admin@jajalmedical.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@jajalmedical.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_invalid_api_login_is_rejected_with_401(): void
    {
        User::factory()->create([
            'email' => 'admin@jajalmedical.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@jajalmedical.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid email or password.',
                'data' => null,
            ]);
    }

    public function test_web_logout_succeeds(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_api_logout_succeeds(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully.',
                'data' => null,
            ]);

        $this->assertCount(0, $user->tokens);
    }
}
