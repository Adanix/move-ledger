<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MoveStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Move extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_code',
        'customer_id',
        'assigned_surveyor_id',
        'created_by_id',
        'status',
        'property_type',
        'origin_address',
        'origin_floor_access',
        'destination_address',
        'destination_floor_access',
        'scheduled_move_date',
        'survey_started_at',
        'origin_signed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'total_items_count',
        'total_boxes_count',
        'total_volume_cuft',
        'total_weight_lbs',
        'total_declared_value',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => MoveStatus::class,
            'property_type' => PropertyType::class,
            'scheduled_move_date' => 'date',
            'survey_started_at' => 'datetime',
            'origin_signed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'total_items_count' => 'integer',
            'total_boxes_count' => 'integer',
            'total_volume_cuft' => 'decimal:2',
            'total_weight_lbs' => 'decimal:2',
            'total_declared_value' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedSurveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_surveyor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(MoveRoom::class)->orderBy('sort_order')->orderBy('id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(MoveShare::class);
    }

    public function activeShare(): HasOne
    {
        return $this->hasOne(MoveShare::class)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->latestOfMany();
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(MoveSignature::class);
    }

    public function validOriginSignature(): HasOne
    {
        return $this->hasOne(MoveSignature::class)
            ->where('signature_stage', 'origin')
            ->where('is_valid', true)
            ->latestOfMany();
    }

    public function validDestinationSignature(): HasOne
    {
        return $this->hasOne(MoveSignature::class)
            ->where('signature_stage', 'destination')
            ->where('is_valid', true)
            ->latestOfMany();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->orderByDesc('created_at');
    }
}
