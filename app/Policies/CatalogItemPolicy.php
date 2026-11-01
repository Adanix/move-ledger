<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CatalogItem;
use App\Models\User;

class CatalogItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, CatalogItem $catalogItem): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }

    public function update(User $user, CatalogItem $catalogItem): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }

    public function delete(User $user, CatalogItem $catalogItem): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }
}
