<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('doctor_timings')) {
            return;
        }

        Schema::create('doctor_timings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id');
            $table->text('day')->nullable();
            $table->string('slot_type', 200)->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('slot_time_gap')->nullable();
            $table->time('first_half_start')->nullable();
            $table->time('first_half_end')->nullable();
            $table->time('second_half_start')->nullable();
            $table->time('second_half_end')->nullable();
            $table->text('generated_slots')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_timings');
    }
};
