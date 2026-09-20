<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wallet_balance')) {
            return;
        }

        Schema::create('wallet_balance', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('doctor_id');
            $table->string('total_recharged', 200);
            $table->string('wallet_balance', 200);
            $table->integer('total_spent')->default(0);
            $table->integer('status')->default(1)->comment(' 0 = inactive, 1 = active ');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_balance');
    }
};
