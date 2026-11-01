<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isCoordinator(): bool
    {
        return $this->role === UserRole::Coordinator;
    }

    public function isSurveyor(): bool
    {
        return $this->role === UserRole::Surveyor;
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        foreach ($roles as $role) {
            $val = $role instanceof UserRole ? $role->value : $role;
            if ($this->role->value === $val) {
                return true;
            }
        }
        return false;
    }

    public function assignedMoves(): HasMany
    {
        return $this->hasMany(Move::class, 'assigned_surveyor_id');
    }

    public function createdMoves(): HasMany
    {
        return $this->hasMany(Move::class, 'created_by_id');
    }
}
