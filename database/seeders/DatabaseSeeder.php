<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Staff Accounts
        User::updateOrCreate(
            ['email' => 'admin@moveledger.test'],
            [
                'name' => 'Chief Admin',
                'phone' => '+1 (555) 100-0001',
                'role' => UserRole::Admin,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'dispatch@moveledger.test'],
            [
                'name' => 'Sarah Coordinator',
                'phone' => '+1 (555) 100-0002',
                'role' => UserRole::Coordinator,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'surveyor@moveledger.test'],
            [
                'name' => 'Alex Surveyor',
                'phone' => '+1 (555) 100-0003',
                'role' => UserRole::Surveyor,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        // 2. Catalog Items
        $this->call(CatalogItemSeeder::class);

        // 3. Sample Moves
        $this->call(MoveSeeder::class);
    }
}
