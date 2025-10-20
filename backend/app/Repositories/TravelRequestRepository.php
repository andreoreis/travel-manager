<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TravelRequest;

class TravelRequestRepository extends AbstractRepository
{
    protected static $model = TravelRequest::class;

     /**
     * Retorna uma query de pedidos de viagem filtrando por status e ID
     *
     * @param array $filters ['status' => string|null, 'user_id' => int|null]
     */
    public function queryWithFilters(array $filters = [])
    {
        $query = self::loadModel()::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('id', $filters['user_id']);
        }

        return $query;
    }
}
