<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('category', 50);
            $table->string('item_type', 40)->default('furniture');
            $table->decimal('default_volume_cuft', 8, 2)->default(0.00);
            $table->decimal('default_weight_lbs', 8, 2)->default(0.00);
            $table->boolean('is_fragile_default')->default(false);
            $table->boolean('requires_disassembly_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
