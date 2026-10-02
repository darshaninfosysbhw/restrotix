<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('identifier', 255);
            $table->string('channel', 20);

            $table->string('otp_hash');

            $table->dateTime('expires_at');
            $table->dateTime('verified_at')->nullable();

            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('resend_count')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'channel']);
            $table->index('identifier');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};