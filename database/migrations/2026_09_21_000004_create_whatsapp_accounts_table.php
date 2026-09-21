<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A Meta WhatsApp Cloud API number owned by a tenant.
     *
     * phone_number_id is unique platform-wide because it is the routing key:
     * an inbound webhook carries it, and it is the only thing that says which
     * tenant the message belongs to.
     *
     * access_token and app_secret are encrypted by casts on the model.
     */
    public function up(): void
    {
        Schema::create('whatsapp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();

            $table->string('phone_number_id')->unique();
            $table->string('waba_id')->nullable();
            $table->string('display_phone_number')->nullable();
            $table->string('meta_business_id')->nullable();

            $table->text('access_token');
            $table->text('app_secret')->nullable();
            $table->string('verify_token');

            $table->string('provider')->default('manual');           // manual|embedded
            $table->string('connection_status')->default('pending'); // pending|connected|failed|disabled
            $table->string('webhook_status')->default('unverified');

            $table->boolean('is_default')->default(false);
            $table->timestamp('last_verified_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'connection_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_accounts');
    }
};
