<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('broadcast_messages')) {
            return;
        }

        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('send_by');
            $table->text('send_to')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->integer('status')->default(1)->comment('1= send, 2= not send');
            $table->string('total_send_messages', 200)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_messages');
    }
};
