<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository extends AbstractRepository
{
    protected static $model = User::class;

    /**
     * Retorna uma query de usuários filtrando por status e ID
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
