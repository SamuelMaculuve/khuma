<?php

namespace App\Enums;

enum Plan : string
{
    case UBUNTU = 'khuma_ubuntu';
    case BAOBA = 'khuma_baoba';
    case LION = 'huma_leao';

    public function label(): string
    {
        return match ($this) {
            self::UBUNTU => 'Plano Ubuntu',
            self::BAOBA => 'Plano Baoba',
            self::LION => 'Plano Leao',
        };
    }
}
