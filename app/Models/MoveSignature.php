<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MoveSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'move_id',
        'signature_stage',
        'signer_name',
        'signer_role',
        'signature_image_path',
        'manifest_snapshot_hash',
        'snapshot_items_count',
        'snapshot_volume_cuft',
        'ip_address',
        'user_agent',
        'is_valid',
        'invalidated_by_id',
        'invalidated_reason',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_items_count' => 'integer',
            'snapshot_volume_cuft' => 'decimal:2',
            'is_valid' => 'boolean',
            'signed_at' => 'datetime',
        ];
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class);
    }

    public function invalidatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invalidated_by_id');
    }

    public function imageUrl(): string
    {
        return Storage::url($this->signature_image_path);
    }
}
