<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Coordinator = 'coordinator';
    case Surveyor = 'surveyor';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Coordinator => 'Dispatcher / Coordinator',
            self::Surveyor => 'Field Surveyor / Crew Lead',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'bg-purple-50 text-purple-700 border-purple-200',
            self::Coordinator => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Surveyor => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
