<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sms_payments')) {
            return;
        }

        Schema::create('sms_payments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id');
            $table->integer('plan_id')->nullable();
            $table->string('transaction_id', 500)->nullable();
            $table->string('amount', 500)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_payments');
    }
};
