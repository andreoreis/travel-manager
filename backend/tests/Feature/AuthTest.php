<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_token()
    {
        $email = 'teste+' . uniqid() . '@exemplo.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => '12345678',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function test_login_validation_errors()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }
}
