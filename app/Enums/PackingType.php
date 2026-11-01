<?php

declare(strict_types=1);

namespace App\Enums;

enum PackingType: string
{
    case CompanyPacked = 'company_packed';
    case Pbo = 'pbo';
    case UnpackedFurniture = 'unpacked_furniture';

    public function label(): string
    {
        return match ($this) {
            self::CompanyPacked => 'Company Packed',
            self::Pbo => 'Packed by Owner (PBO)',
            self::UnpackedFurniture => 'Pad / Blanket Wrap (Furniture)',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::CompanyPacked => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Pbo => 'bg-amber-50 text-amber-800 border-amber-300',
            self::UnpackedFurniture => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }
}
