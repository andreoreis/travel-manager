<?php

namespace App\Enums;

enum TravelRequestStatus: string
{
    case SOLICITADO = 'solicitado';
    case APROVADO   = 'aprovado';
    case CANCELADO  = 'cancelado';

    /**
     * @return array Contendo os valores das cases (ex.: ['solicitado','aprovado','cancelado'])
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
