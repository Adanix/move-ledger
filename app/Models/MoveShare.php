<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class MoveShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'move_id',
        'created_by_id',
        'token',
        'access_pin_hash',
        'has_pin',
        'allow_customer_signoff',
        'expires_at',
        'revoked_at',
        'last_accessed_at',
        'access_count',
    ];

    protected function casts(): array
    {
        return [
            'has_pin' => 'boolean',
            'allow_customer_signoff' => 'boolean',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'access_count' => 'integer',
        ];
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isValid(): bool
    {
        return ! $this->isRevoked() && ! $this->isExpired();
    }

    public function verifyPin(string $pin): bool
    {
        if (! $this->has_pin || ! $this->access_pin_hash) {
            return true;
        }

        return Hash::check($pin, $this->access_pin_hash);
    }
}
