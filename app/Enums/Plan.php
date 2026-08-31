<?php

namespace App\Enums;

enum Plan : string
{
    case UBUNTU = 'ubuntu';
    case BAOBA = 'baoba';
    case LION = 'leao';

    public function label(): string
    {
        return match ($this) {
            self::UBUNTU => 'Plano Ubuntu',
            self::BAOBA => 'Plano Baoba',
            self::LION => 'Plano Leao',
        };
    }
}
