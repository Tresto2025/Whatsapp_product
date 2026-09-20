<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sms_logs')) {
            return;
        }

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id');
            $table->string('sms_to')->nullable();
            $table->string('sms_from')->nullable();
            $table->string('sid')->nullable();
            $table->text('body')->nullable();
            $table->string('status', 200)->nullable();
            $table->text('broadcast_id')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
