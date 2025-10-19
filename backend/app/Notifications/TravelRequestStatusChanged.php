<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TravelRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public TravelRequest $travelRequest;
    public string $oldStatus;
    public string $newStatus;

    /**
     * @param TravelRequest $travelRequest
     * @param string $oldStatus
     * @param string $newStatus
     */
    public function __construct(TravelRequest $travelRequest, string $oldStatus, string $newStatus)
    {
        $this->travelRequest = $travelRequest;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;

        // Garante que o job só será dispatchado depois do commit da transação
        $this->afterCommit();
    }

    /**
     * Apenas database channel (persistido na tabela notifications).
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Payload salvo na tabela `notifications`.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        $tr = $this->travelRequest;

        return [
            'travel_request_id' => $tr->id,
            'destination'       => $tr->destination,
            'old_status'        => $this->oldStatus,
            'new_status'        => $this->newStatus,
            'start_date'        => $tr->start_date?->toDateString(),
            'end_date'          => $tr->end_date?->toDateString(),
            'message'           => "O status do seu pedido #{$tr->id} foi alterado para {$this->newStatus}.",
        ];
    }
}
