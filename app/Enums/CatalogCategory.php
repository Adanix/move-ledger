<?php

declare(strict_types=1);

namespace App\Enums;

enum CatalogCategory: string
{
    case LivingRoom = 'living_room';
    case Bedroom = 'bedroom';
    case KitchenDining = 'kitchen_dining';
    case Office = 'office';
    case Appliances = 'appliances';
    case OutdoorGarage = 'outdoor_garage';
    case BoxesContainers = 'boxes_containers';
    case SpecialtyFragile = 'specialty_fragile';

    public function label(): string
    {
        return match ($this) {
            self::LivingRoom => 'Living Room',
            self::Bedroom => 'Bedroom',
            self::KitchenDining => 'Kitchen & Dining',
            self::Office => 'Office',
            self::Appliances => 'Appliances',
            self::OutdoorGarage => 'Outdoor & Garage',
            self::BoxesContainers => 'Boxes & Containers',
            self::SpecialtyFragile => 'Specialty & Fragile',
        };
    }
}
