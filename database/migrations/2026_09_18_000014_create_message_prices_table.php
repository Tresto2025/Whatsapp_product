<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('message_prices')) {
            return;
        }

        Schema::create('message_prices', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('price_per_message')->default(1);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_prices');
    }
};
