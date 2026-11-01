<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MoveStatus;
use App\Models\AuditLog;
use App\Models\Move;
use App\Models\MoveSignature;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SignatureVerificationService
{
    /**
     * Build canonical deterministic snapshot and SHA-256 hash of the inventory at sign-off.
     *
     * @return array{hash: string, items_count: int, volume_cuft: float, payload: array}
     */
    public function generateManifestSnapshot(Move $move): array
    {
        $move->loadMissing(['rooms.items' => fn ($q) => $q->orderBy('id')]);

        $roomsData = [];
        $totalItems = 0;
        $totalVolume = 0.00;

        foreach ($move->rooms as $room) {
            $itemsData = [];
            foreach ($room->items as $item) {
                $totalItems += $item->quantity;
                $totalVolume += ($item->quantity * (float) $item->unit_volume_cuft);

                $itemsData[] = [
                    'id' => $item->id,
                    'tag_number' => $item->tag_number,
                    'name' => $item->name,
                    'category' => $item->item_category->value,
                    'quantity' => $item->quantity,
                    'volume_cuft' => (string) $item->unit_volume_cuft,
                    'weight_lbs' => (string) $item->unit_weight_lbs,
                    'packing_type' => $item->packing_type->value,
                    'is_fragile' => $item->is_fragile,
                    'is_high_value' => $item->is_high_value,
                    'condition' => $item->origin_condition->value,
                    'condition_notes' => $item->condition_notes ?? '',
                    'declared_value' => $item->declared_value ? (string) $item->declared_value : null,
                ];
            }

            $roomsData[] = [
                'id' => $room->id,
                'name' => $room->name,
                'floor_level' => $room->floor_level,
                'items' => $itemsData,
            ];
        }

        $snapshot = [
            'move_id' => $move->id,
            'reference_code' => $move->reference_code,
            'origin_address' => $move->origin_address,
            'destination_address' => $move->destination_address,
            'rooms' => $roomsData,
            'generated_at' => now()->toIso8601String(),
        ];

        $jsonPayload = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = hash('sha256', (string) $jsonPayload);

        return [
            'hash' => $hash,
            'items_count' => $totalItems,
            'volume_cuft' => round($totalVolume, 2),
            'payload' => $snapshot,
        ];
    }

    /**
     * Decode, validate, and store signature image and record.
     */
    public function storeSignature(
        Move $move,
        string $stage,
        string $signerName,
        string $signerRole,
        string $signatureDataUri,
        ?string $ip = null,
        ?string $userAgent = null,
        ?User $staffUser = null
    ): MoveSignature {
        if (! in_array($stage, ['origin', 'destination'], true)) {
            throw new InvalidArgumentException("Invalid signature stage '{$stage}'.");
        }

        // Validate base64 data URI format
        if (! str_starts_with($signatureDataUri, 'data:image/png;base64,')) {
            throw new InvalidArgumentException('Invalid signature format. PNG Data URI required.');
        }

        $base64Data = substr($signatureDataUri, strlen('data:image/png;base64,'));
        $binaryData = base64_decode($base64Data, true);

        if ($binaryData === false || strlen($binaryData) < 100) {
            throw new InvalidArgumentException('Signature drawing appears empty. Please sign before submitting.');
        }

        if (strlen($binaryData) > 512 * 1024) {
            throw new InvalidArgumentException('Signature image payload exceeds 500 KB limit.');
        }

        // Verify valid image bytes
        $imageInfo = @getimagesizefromstring($binaryData);
        if ($imageInfo === false || $imageInfo['mime'] !== 'image/png') {
            throw new InvalidArgumentException('Decoded signature payload is not a valid PNG image.');
        }

        return DB::transaction(function () use (
            $move,
            $stage,
            $signerName,
            $signerRole,
            $binaryData,
            $ip,
            $userAgent,
            $staffUser
        ): MoveSignature {
            // Lock move row
            Move::where('id', $move->id)->lockForUpdate()->first();

            // Store signature image on public disk
            $filename = sprintf(
                'moves/%d/signatures/sig_%s_%s_%s.png',
                $move->id,
                $stage,
                now()->format('Ymd_His'),
                Str::random(8)
            );

            Storage::disk('public')->put($filename, $binaryData);

            $snapshot = $this->generateManifestSnapshot($move);

            // Invalidate any previous signatures for this stage
            MoveSignature::where('move_id', $move->id)
                ->where('signature_stage', $stage)
                ->where('is_valid', true)
                ->update(['is_valid' => false]);

            $signature = MoveSignature::create([
                'move_id' => $move->id,
                'signature_stage' => $stage,
                'signer_name' => $signerName,
                'signer_role' => $signerRole,
                'signature_image_path' => $filename,
                'manifest_snapshot_hash' => $snapshot['hash'],
                'snapshot_items_count' => $snapshot['items_count'],
                'snapshot_volume_cuft' => $snapshot['volume_cuft'],
                'ip_address' => $ip,
                'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
                'is_valid' => true,
                'signed_at' => now(),
            ]);

            // Transition move status based on stage
            if ($stage === 'origin') {
                $move->update([
                    'status' => MoveStatus::OriginSigned,
                    'origin_signed_at' => now(),
                ]);
            } elseif ($stage === 'destination') {
                $move->update([
                    'status' => MoveStatus::Delivered,
                    'completed_at' => now(),
                ]);
            }

            AuditLog::log(
                eventType: "signature.captured",
                description: ucfirst($stage) . " signature captured from {$signerName} ({$signerRole}).",
                move: $move,
                user: $staffUser,
                metadata: [
                    'stage' => $stage,
                    'signer_name' => $signerName,
                    'signer_role' => $signerRole,
                    'snapshot_hash' => $snapshot['hash'],
                    'ip' => $ip,
                ],
                actorName: $signerName
            );

            return $signature;
        });
    }
}
