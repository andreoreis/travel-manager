<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TravelRequestStatusChanged;

class TravelStatusChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create(['is_admin' => true]);
        $travel = TravelRequest::factory()->create([
            'status' => 'solicitado',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'api')->patchJson("/api/travel-requests/{$travel->id}/status", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(200);

        // verifica que a notificação foi enviada para o usuário do pedido
        Notification::assertSentTo(
            [$user],
            TravelRequestStatusChanged::class,
            function ($notification, $channels) use ($travel) {
                return true;
            }
        );

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travel->id,
            'status' => 'aprovado',
        ]);
    }
}
