<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_signatures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('move_id')->constrained('moves')->cascadeOnDelete();
            $table->string('signature_stage', 20); // origin, destination
            $table->string('signer_name', 150);
            $table->string('signer_role', 40)->default('customer');
            $table->string('signature_image_path', 255);
            $table->char('manifest_snapshot_hash', 64);
            $table->unsignedInteger('snapshot_items_count')->default(0);
            $table->decimal('snapshot_volume_cuft', 10, 2)->default(0.00);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->boolean('is_valid')->default(true);
            $table->foreignId('invalidated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('invalidated_reason', 500)->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();

            $table->index(['move_id', 'signature_stage', 'is_valid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_signatures');
    }
};
