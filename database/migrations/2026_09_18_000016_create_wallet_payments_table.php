<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wallet_payments')) {
            return;
        }

        Schema::create('wallet_payments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id');
            $table->string('transaction_id', 200);
            $table->string('amount', 200);
            $table->string('payment_gateway', 200);
            $table->integer('status')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_payments');
    }
};
