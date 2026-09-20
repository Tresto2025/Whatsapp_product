<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('doctor_service')) {
            return;
        }

        Schema::create('doctor_service', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id');
            $table->integer('service_id')->nullable();
            $table->text('service_name')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_service');
    }
};
