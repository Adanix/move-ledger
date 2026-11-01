<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'move_id',
        'user_id',
        'actor_name',
        'event_type',
        'description',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        string $eventType,
        string $description,
        ?Move $move = null,
        ?User $user = null,
        ?array $metadata = null,
        ?string $actorName = null
    ): self {
        $user = $user ?? auth()->user();
        $name = $actorName ?? ($user ? $user->name : 'System / Customer');

        return self::create([
            'move_id' => $move?->id,
            'user_id' => $user?->id,
            'actor_name' => $name,
            'event_type' => $eventType,
            'description' => $description,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
