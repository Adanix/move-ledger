<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MoveStatus;
use App\Models\AuditLog;
use App\Models\Move;
use App\Models\MoveRoom;
use App\Models\MoveShare;
use App\Models\MoveSignature;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MoveLifecycleService
{
    /**
     * Generate unique reference code: MV-YYYY-NNNNN
     */
    public function generateReferenceCode(): string
    {
        $year = date('Y');
        $prefix = "MV-{$year}-";

        return DB::transaction(function () use ($prefix): string {
            $lastMove = Move::where('reference_code', 'LIKE', "{$prefix}%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextNum = 1;
            if ($lastMove && preg_match('/MV-\d{4}-(\d+)/', $lastMove->reference_code, $matches)) {
                $nextNum = ((int) $matches[1]) + 1;
            } else {
                $count = Move::count();
                $nextNum = $count + 1;
            }

            return sprintf('%s%05d', $prefix, $nextNum);
        });
    }

    /**
     * Seed default rooms for a newly created move based on property type.
     */
    public function seedDefaultRooms(Move $move): void
    {
        $rooms = $move->property_type->defaultRooms();

        foreach ($rooms as $roomData) {
            MoveRoom::create([
                'move_id' => $move->id,
                'name' => $roomData['name'],
                'floor_level' => $roomData['floor_level'],
                'sort_order' => $roomData['sort_order'],
                'room_items_count' => 0,
                'room_volume_cuft' => 0.00,
            ]);
        }
    }

    /**
     * Safely transition a move's status.
     */
    public function transitionStatus(Move $move, MoveStatus $target, ?string $reason = null, ?User $actor = null): void
    {
        if ($move->status === $target) {
            return;
        }

        if (! $move->status->canTransitionTo($target) && $target !== MoveStatus::Cancelled) {
            throw new InvalidArgumentException(
                "Invalid status transition from '{$move->status->label()}' to '{$target->label()}'."
            );
        }

        DB::transaction(function () use ($move, $target, $reason, $actor): void {
            $oldStatus = $move->status;
            $updates = ['status' => $target];

            if ($target === MoveStatus::InSurvey && $move->survey_started_at === null) {
                $updates['survey_started_at'] = now();
            }

            if ($target === MoveStatus::Cancelled) {
                $updates['cancelled_at'] = now();
                $updates['cancellation_reason'] = $reason ?? 'Cancelled by coordinator/admin';
            }

            if ($target === MoveStatus::Delivered) {
                $updates['completed_at'] = now();
            }

            $move->update($updates);

            AuditLog::log(
                eventType: 'status.changed',
                description: "Status changed from '{$oldStatus->label()}' to '{$target->label()}'.",
                move: $move,
                user: $actor,
                metadata: [
                    'from' => $oldStatus->value,
                    'to' => $target->value,
                    'reason' => $reason,
                ]
            );
        });
    }

    /**
     * Unlock an origin_signed move for revision, invalidating prior signatures.
     */
    public function unlockForRevision(Move $move, string $reason, ?User $actor = null): void
    {
        DB::transaction(function () use ($move, $reason, $actor): void {
            // Invalidate all active origin signatures
            MoveSignature::where('move_id', $move->id)
                ->where('signature_stage', 'origin')
                ->where('is_valid', true)
                ->update([
                    'is_valid' => false,
                    'invalidated_by_id' => $actor?->id ?? auth()->id(),
                    'invalidated_reason' => $reason,
                ]);

            $move->update([
                'status' => MoveStatus::InSurvey,
                'origin_signed_at' => null,
            ]);

            AuditLog::log(
                eventType: 'inventory.unlocked',
                description: "Signed inventory unlocked for revision: {$reason}",
                move: $move,
                user: $actor,
                metadata: ['reason' => $reason]
            );
        });
    }

    /**
     * Generate or rotate customer share link.
     */
    public function createShareLink(
        Move $move,
        ?string $pin = null,
        int $expireDays = 14,
        bool $allowSign = true,
        ?User $actor = null
    ): MoveShare {
        return DB::transaction(function () use ($move, $pin, $expireDays, $allowSign, $actor): MoveShare {
            // Revoke any previous active share links
            MoveShare::where('move_id', $move->id)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            $token = Str::random(64);
            $hasPin = ! empty($pin);
            $pinHash = $hasPin ? Hash::make($pin) : null;

            $share = MoveShare::create([
                'move_id' => $move->id,
                'created_by_id' => $actor?->id ?? auth()->id(),
                'token' => $token,
                'access_pin_hash' => $pinHash,
                'has_pin' => $hasPin,
                'allow_customer_signoff' => $allowSign,
                'expires_at' => now()->addDays($expireDays),
            ]);

            AuditLog::log(
                eventType: 'share.created',
                description: "Customer share link generated (PIN protected: " . ($hasPin ? 'Yes' : 'No') . ")",
                move: $move,
                user: $actor,
                metadata: [
                    'share_id' => $share->id,
                    'expires_at' => $share->expires_at->toIso8601String(),
                    'has_pin' => $hasPin,
                ]
            );

            return $share;
        });
    }
}
