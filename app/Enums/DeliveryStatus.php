<?php

declare(strict_types=1);

namespace App\Enums;

enum DeliveryStatus: string
{
    case Pending = 'pending';
    case Delivered = 'delivered';
    case DamagedInTransit = 'damaged_in_transit';
    case Missing = 'missing';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending Unload',
            self::Delivered => 'Delivered',
            self::DamagedInTransit => 'Damaged in Transit',
            self::Missing => 'Missing',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-slate-100 text-slate-700 border-slate-300',
            self::Delivered => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::DamagedInTransit => 'bg-amber-50 text-amber-800 border-amber-300',
            self::Missing => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
