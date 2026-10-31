<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Perti = 'perti';
    case Prodi = 'prodi';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Perti => 'Perguruan Tinggi',
            self::Prodi => 'Program Studi',
        };
    }
}
