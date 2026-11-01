<?php

declare(strict_types=1);

namespace App\Enums;

enum OriginCondition: string
{
    case Good = 'good';
    case FairWorn = 'fair_worn';
    case PreExistingDamage = 'pre_existing_damage';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good / Normal Wear',
            self::FairWorn => 'Fair / Aged Wear',
            self::PreExistingDamage => 'Pre-Existing Damage',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Good => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::FairWorn => 'bg-slate-100 text-slate-700 border-slate-300',
            self::PreExistingDamage => 'bg-amber-50 text-amber-800 border-amber-300',
        };
    }
}
