<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_sessions')) {
            return;
        }

        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('doctor_id')->nullable();
            $table->string('phone', 20);
            $table->string('step', 50)->nullable()->default('awaiting_name');
            $table->string('mode', 999)->nullable()->default('booking');
            $table->longText('data')->nullable();
            $table->string('completed', 250)->nullable();
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('phone', 'phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
