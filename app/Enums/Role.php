<?php

namespace App\Enums;

enum Role : string
{
    case ADMIN = 'admin';
    case CLIENT_MANAGER = 'subscriber';
    case CLIENT_EMPLOYEE = 'salesperson';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::CLIENT_MANAGER => 'Gestor',
            self::CLIENT_EMPLOYEE => 'Vendedor',
        };
    }
}
