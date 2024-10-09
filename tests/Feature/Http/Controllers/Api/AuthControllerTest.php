<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function test_successful_registration(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_unsuccessful_registration_due_to_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['email']);
    }

    public function test_successful_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'correct-password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    public function test_unsuccessful_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    public function test_successful_logout(): void
    {
        $token = auth()->login($this->user);

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Successfully logged out',
        ]);
    }

    public function test_unsuccessful_logout_without_token(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_successful_token_refresh(): void
    {
        $token = auth()->login($this->user);

        $response = $this->postJson('/api/refresh', [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    public function test_unsuccessful_token_refresh_without_token(): void
    {
        $response = $this->postJson('/api/refresh');

        $response->assertStatus(401);
    }

    public function test_fetch_user_details(): void
    {
        $token = auth()->login($this->user);

        $response = $this->postJson('/api/me', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'email' => $this->user->email,
            'name' => $this->user->name,
        ]);
    }

    public function test_unsuccessful_fetch_user_details_without_token(): void
    {
        $response = $this->postJson('/api/me');

        $response->assertStatus(401);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'correct-password',
        ]);
    }
}
