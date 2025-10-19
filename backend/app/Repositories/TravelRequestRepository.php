<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TravelRequest;

class TravelRequestRepository extends AbstractRepository
{
    protected static $model = TravelRequest::class;
}
