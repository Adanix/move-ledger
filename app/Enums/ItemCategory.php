<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemCategory: string
{
    case Furniture = 'furniture';
    case Appliance = 'appliance';
    case Electronics = 'electronics';
    case BoxCarton = 'box_carton';
    case FragileArt = 'fragile_art';
    case Outdoor = 'outdoor';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Furniture => 'Furniture',
            self::Appliance => 'Major Appliance',
            self::Electronics => 'Electronics / TV',
            self::BoxCarton => 'Box / Carton',
            self::FragileArt => 'Fragile / Artwork / Mirror',
            self::Outdoor => 'Outdoor / Lawn / Tools',
            self::Other => 'Miscellaneous / Other',
        };
    }
}
