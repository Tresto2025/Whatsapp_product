<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('appointments')) {
            return;
        }

        Schema::create('appointments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id')->nullable();
            $table->text('service_type')->nullable();
            $table->text('phone')->nullable();
            $table->text('name')->nullable();
            $table->text('date')->nullable();
            $table->text('time')->nullable();
            $table->time('start_time')->nullable();
            $table->text('new_date')->nullable();
            $table->text('new_time')->nullable();
            $table->text('purpose')->nullable();
            $table->integer('status')->default(1)
                ->comment('0 = cancel, 1 = approved, 2 = reschedule request, 3 = checkin, 4 = missing');
            $table->integer('is_reschedule')->default(0)->comment('0 = not reschedule, 1= reschedule');
            $table->text('cancel_reason')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
