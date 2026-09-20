<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sms_balance')) {
            return;
        }

        Schema::create('sms_balance', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id');
            $table->string('total_sms', 200);
            $table->string('pending_sms', 200);
            $table->string('spent_sms', 200)->default('0');
            $table->integer('status')->default(1)->comment('0 = inactive, 1 = active');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_balance');
    }
};
