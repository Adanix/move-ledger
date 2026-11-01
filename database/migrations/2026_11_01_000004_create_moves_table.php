<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moves', function (Blueprint $table): void {
            $table->id();
            $table->string('reference_code', 30)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('assigned_surveyor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft');
            $table->string('property_type', 40)->default('house_3br');
            $table->string('origin_address', 500);
            $table->string('origin_floor_access', 150)->nullable();
            $table->string('destination_address', 500);
            $table->string('destination_floor_access', 150)->nullable();
            $table->date('scheduled_move_date')->index();
            $table->timestamp('survey_started_at')->nullable();
            $table->timestamp('origin_signed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason', 500)->nullable();
            $table->unsignedInteger('total_items_count')->default(0);
            $table->unsignedInteger('total_boxes_count')->default(0);
            $table->decimal('total_volume_cuft', 10, 2)->default(0.00);
            $table->decimal('total_weight_lbs', 10, 2)->default(0.00);
            $table->decimal('total_declared_value', 12, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'scheduled_move_date']);
            $table->index('assigned_surveyor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moves');
    }
};
