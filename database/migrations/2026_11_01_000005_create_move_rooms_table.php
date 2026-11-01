<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('move_id')->constrained('moves')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('floor_level', 50)->nullable()->default('Main Floor');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->unsignedInteger('room_items_count')->default(0);
            $table->decimal('room_volume_cuft', 10, 2)->default(0.00);
            $table->timestamps();

            $table->index(['move_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_rooms');
    }
};
