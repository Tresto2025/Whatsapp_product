<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('message_plans')) {
            return;
        }

        Schema::create('message_plans', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('plan_name');
            $table->string('no_of_messages');
            $table->string('price');
            $table->text('description')->nullable();
            $table->integer('status')->default(1)->comment('0= inactive, 1 = active');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_plans');
    }
};
