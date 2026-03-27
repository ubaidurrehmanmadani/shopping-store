<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_api_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Mobile User',
            'email' => 'mobile@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'iphone-15',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'mobile@example.com')
            ->assertJsonPath('data.token.token_type', 'Bearer');

        $this->assertDatabaseHas('users', [
            'email' => 'mobile@example.com',
        ]);
        $this->assertDatabaseCount('api_tokens', 1);
    }

    public function test_authenticated_user_can_fetch_profile_and_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->issueApiToken('android')['access_token'];

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('api_tokens', 0);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }
}
