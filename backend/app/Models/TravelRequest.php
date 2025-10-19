<?php

namespace App\Models;

use App\Enums\TravelRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelRequest extends Model
{
    protected $table = 'travel_requests';

    protected $fillable = [
        'user_id',
        'requester_name',
        'destination',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // Helper para retornar enum instance
    public function getStatusAttribute($value): TravelRequestStatus
    {
        return TravelRequestStatus::from($value);
    }

    public function setStatusAttribute(TravelRequestStatus|string $value): void
    {
        $this->attributes['status'] = $value instanceof TravelRequestStatus ? $value->value : $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
