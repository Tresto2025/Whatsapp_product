<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('trainer_profession')) {
            return;
        }

        Schema::create('trainer_profession', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('profession')->nullable();
            $table->integer('status')->default(1)->comment('1= active');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_profession');
    }
};
