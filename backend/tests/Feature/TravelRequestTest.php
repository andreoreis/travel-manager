<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_travel_request()
    {
        $user = User::factory()->create();

        $payload = [
            'requester_name' => 'André Reis',
            'destination' => 'São Paulo',
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'solicitado',
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/travel-requests', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('travel_requests', [
            'user_id' => $user->id,
            'requester_name' => 'André Reis',
            'destination' => 'São Paulo',
            'status' => 'solicitado',
        ]);
    }

    public function test_validation_fails_when_required_fields_are_missing()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/travel-requests', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'requester_name',
            'destination',
            'start_date',
        ]);
    }

    public function test_authenticated_user_can_update_travel_request()
    {
        $user = User::factory()->create();
        $travel = TravelRequest::factory()->create([
            'user_id' => $user->id,
            'requester_name' => 'André Reis',
            'destination' => 'São Paulo',
            'status' => 'pending',
        ]);

        $update = [
            'requester_name' => 'André Reis',
            'destination' => 'Rio de Janeiro',
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'aprovado',
        ];

        $response = $this->actingAs($user, 'api')->putJson("/api/travel-requests/{$travel->id}", $update);

        $response->assertStatus(200);
        $this->assertDatabaseHas('travel_requests', [
            'id' => $travel->id,
            'destination' => 'Rio de Janeiro',
            'status' => 'aprovado',
        ]);
    }
}
