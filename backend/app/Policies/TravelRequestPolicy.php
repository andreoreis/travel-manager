<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TravelRequest;

class TravelRequestPolicy
{
    /**
     * Verifica se o usuário pode visualizar o pedido.
     *
     * @param User $user
     * @param TravelRequest $travelRequest
     * @return bool
     */
    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $travelRequest->user_id === $user->id;
    }

    /**
     * Verifica se o usuário pode cancelar o pedido.
     *
     * @param User $user
     * @param TravelRequest $travelRequest
     * @return bool
     */
    public function cancel(User $user, TravelRequest $travelRequest)
    {
        return $user->is_admin || $travelRequest->user_id === $user->id;
    }
}
