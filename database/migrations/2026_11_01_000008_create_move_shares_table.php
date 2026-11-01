<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_shares', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('move_id')->constrained('moves')->cascadeOnDelete();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token', 64)->unique();
            $table->string('access_pin_hash', 255)->nullable();
            $table->boolean('has_pin')->default(false);
            $table->boolean('allow_customer_signoff')->default(true);
            $table->timestamp('expires_at')->index();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->unsignedInteger('access_count')->default(0);
            $table->timestamps();

            $table->index('move_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_shares');
    }
};
