<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 500)->nullable();
            $table->string('email', 500)->nullable();
            $table->string('phone', 200)->nullable();
            $table->string('amount', 200)->nullable();
            $table->text('razorpay_payment_id')->nullable();
            $table->string('order_id', 500)->nullable();
            $table->integer('status')->default(1);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
