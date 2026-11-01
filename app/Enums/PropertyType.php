<?php

declare(strict_types=1);

namespace App\Enums;

enum PropertyType: string
{
    case Studio = 'studio';
    case Apartment1Br = 'apartment_1br';
    case Apartment2Br = 'apartment_2br';
    case House3Br = 'house_3br';
    case House4BrPlus = 'house_4br_plus';
    case Office = 'office';

    public function label(): string
    {
        return match ($this) {
            self::Studio => 'Studio Apartment',
            self::Apartment1Br => '1-Bedroom Apartment',
            self::Apartment2Br => '2-Bedroom Apartment',
            self::House3Br => '3-Bedroom House',
            self::House4BrPlus => '4+ Bedroom House',
            self::Office => 'Commercial / Office',
        };
    }

    /**
     * @return array<int, array{name: string, floor_level: string, sort_order: int}>
     */
    public function defaultRooms(): array
    {
        return match ($this) {
            self::Studio => [
                ['name' => 'Main Living Area', 'floor_level' => 'Main Floor', 'sort_order' => 1],
                ['name' => 'Kitchenette', 'floor_level' => 'Main Floor', 'sort_order' => 2],
                ['name' => 'Bathroom & Closet', 'floor_level' => 'Main Floor', 'sort_order' => 3],
                ['name' => 'Boxes & Miscellaneous', 'floor_level' => 'Main Floor', 'sort_order' => 4],
            ],
            self::Apartment1Br => [
                ['name' => 'Living Room', 'floor_level' => 'Main Floor', 'sort_order' => 1],
                ['name' => 'Bedroom', 'floor_level' => 'Main Floor', 'sort_order' => 2],
                ['name' => 'Kitchen & Dining', 'floor_level' => 'Main Floor', 'sort_order' => 3],
                ['name' => 'Bathroom & Storage', 'floor_level' => 'Main Floor', 'sort_order' => 4],
                ['name' => 'Boxes & Miscellaneous', 'floor_level' => 'Main Floor', 'sort_order' => 5],
            ],
            self::Apartment2Br => [
                ['name' => 'Living Room', 'floor_level' => 'Main Floor', 'sort_order' => 1],
                ['name' => 'Kitchen & Dining', 'floor_level' => 'Main Floor', 'sort_order' => 2],
                ['name' => 'Master Bedroom', 'floor_level' => 'Main Floor', 'sort_order' => 3],
                ['name' => 'Bedroom 2', 'floor_level' => 'Main Floor', 'sort_order' => 4],
                ['name' => 'Balcony & Storage', 'floor_level' => 'Main Floor', 'sort_order' => 5],
                ['name' => 'Boxes & Miscellaneous', 'floor_level' => 'Main Floor', 'sort_order' => 6],
            ],
            self::House3Br => [
                ['name' => 'Living Room', 'floor_level' => 'Main Floor', 'sort_order' => 1],
                ['name' => 'Kitchen & Dining', 'floor_level' => 'Main Floor', 'sort_order' => 2],
                ['name' => 'Master Bedroom', 'floor_level' => '2nd Floor', 'sort_order' => 3],
                ['name' => 'Bedroom 2', 'floor_level' => '2nd Floor', 'sort_order' => 4],
                ['name' => 'Bedroom 3', 'floor_level' => '2nd Floor', 'sort_order' => 5],
                ['name' => 'Garage / Storage', 'floor_level' => 'Ground Floor', 'sort_order' => 6],
                ['name' => 'Boxes & Miscellaneous', 'floor_level' => 'Main Floor', 'sort_order' => 7],
            ],
            self::House4BrPlus => [
                ['name' => 'Formal Living Room', 'floor_level' => 'Main Floor', 'sort_order' => 1],
                ['name' => 'Family Room', 'floor_level' => 'Main Floor', 'sort_order' => 2],
                ['name' => 'Kitchen & Dining Area', 'floor_level' => 'Main Floor', 'sort_order' => 3],
                ['name' => 'Master Bedroom Suite', 'floor_level' => '2nd Floor', 'sort_order' => 4],
                ['name' => 'Bedroom 2', 'floor_level' => '2nd Floor', 'sort_order' => 5],
                ['name' => 'Bedroom 3', 'floor_level' => '2nd Floor', 'sort_order' => 6],
                ['name' => 'Guest Bedroom', 'floor_level' => '2nd Floor', 'sort_order' => 7],
                ['name' => 'Home Office / Den', 'floor_level' => 'Main Floor', 'sort_order' => 8],
                ['name' => 'Basement / Rec Room', 'floor_level' => 'Basement', 'sort_order' => 9],
                ['name' => 'Garage & Patio', 'floor_level' => 'Ground Floor', 'sort_order' => 10],
                ['name' => 'Boxes & Miscellaneous', 'floor_level' => 'Main Floor', 'sort_order' => 11],
            ],
            self::Office => [
                ['name' => 'Reception & Lobby', 'floor_level' => 'Main Floor', 'sort_order' => 1],
                ['name' => 'Executive Office', 'floor_level' => 'Main Floor', 'sort_order' => 2],
                ['name' => 'Open Workstations', 'floor_level' => 'Main Floor', 'sort_order' => 3],
                ['name' => 'Conference Room', 'floor_level' => 'Main Floor', 'sort_order' => 4],
                ['name' => 'Server & IT Room', 'floor_level' => 'Main Floor', 'sort_order' => 5],
                ['name' => 'Breakroom & Pantry', 'floor_level' => 'Main Floor', 'sort_order' => 6],
                ['name' => 'Storage & File Archives', 'floor_level' => 'Main Floor', 'sort_order' => 7],
            ],
        };
    }
}
