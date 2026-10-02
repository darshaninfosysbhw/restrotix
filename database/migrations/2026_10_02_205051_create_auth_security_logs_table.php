<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_security_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('event_type', 100);

            $table->string('channel', 20)->nullable();

            $table->string('identifier_hash', 64)->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->text('user_agent')->nullable();

            $table->unsignedTinyInteger('attempt_number')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('event_type');
            $table->index('user_id');
            $table->index('identifier_hash');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_security_logs');
    }
};