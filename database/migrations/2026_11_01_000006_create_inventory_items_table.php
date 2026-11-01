<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('move_id')->constrained('moves')->cascadeOnDelete();
            $table->foreignId('move_room_id')->constrained('move_rooms')->cascadeOnDelete();
            $table->foreignId('catalog_item_id')->nullable()->constrained('catalog_items')->nullOnDelete();
            $table->string('tag_number', 20);
            $table->string('name', 150);
            $table->string('item_category', 40)->default('furniture');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_volume_cuft', 8, 2)->default(0.00);
            $table->decimal('unit_weight_lbs', 8, 2)->default(0.00);
            $table->string('packing_type', 30)->default('unpacked_furniture');
            $table->boolean('is_fragile')->default(false);
            $table->boolean('is_high_value')->default(false);
            $table->boolean('requires_disassembly')->default(false);
            $table->decimal('declared_value', 12, 2)->nullable();
            $table->string('origin_condition', 30)->default('good');
            $table->text('condition_notes')->nullable();
            $table->string('delivery_status', 30)->default('pending');
            $table->text('delivery_exception_notes')->nullable();
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['move_id', 'tag_number']);
            $table->index(['move_id', 'delivery_status']);
            $table->index('move_room_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
