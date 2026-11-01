<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MoveStatus;
use App\Models\Move;
use App\Models\User;

class MovePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Move $move): bool
    {
        if ($user->isAdmin() || $user->isCoordinator()) {
            return true;
        }

        return $move->assigned_surveyor_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }

    public function update(User $user, Move $move): bool
    {
        return $user->isAdmin() || $user->isCoordinator();
    }

    public function delete(User $user, Move $move): bool
    {
        if (! ($user->isAdmin() || $user->isCoordinator())) {
            return false;
        }

        return $move->status === MoveStatus::Draft && $move->signatures()->count() === 0;
    }

    public function manageInventory(User $user, Move $move): bool
    {
        if (! $move->status->canEditInventory()) {
            return false;
        }

        if ($user->isAdmin() || $user->isCoordinator()) {
            return true;
        }

        return $move->assigned_surveyor_id === $user->id;
    }

    public function updateStatus(User $user, Move $move): bool
    {
        if ($user->isAdmin() || $user->isCoordinator()) {
            return true;
        }

        return $move->assigned_surveyor_id === $user->id;
    }

    public function manageDelivery(User $user, Move $move): bool
    {
        if (! in_array($move->status, [MoveStatus::OriginSigned, MoveStatus::InTransit, MoveStatus::Delivered], true)) {
            return false;
        }

        if ($user->isAdmin() || $user->isCoordinator()) {
            return true;
        }

        return $move->assigned_surveyor_id === $user->id;
    }

    public function captureSignature(User $user, Move $move): bool
    {
        if ($user->isAdmin() || $user->isCoordinator()) {
            return true;
        }

        return $move->assigned_surveyor_id === $user->id;
    }

    public function share(User $user, Move $move): bool
    {
        if ($user->isAdmin() || $user->isCoordinator()) {
            return true;
        }

        return $move->assigned_surveyor_id === $user->id;
    }
}
