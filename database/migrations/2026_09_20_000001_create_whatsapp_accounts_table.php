<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One Meta WhatsApp Cloud API number per row, owned by a tenant.
     *
     * phone_number_id is the routing key: inbound webhooks carry it in
     * entry[].changes[].value.metadata.phone_number_id, and it is what maps a
     * message back to the tenant that owns the number. It is therefore unique
     * across the whole platform, not just within a tenant.
     *
     * access_token and app_secret are encrypted at rest via Eloquent casts on
     * the WhatsappAccount model, so they are unreadable in a database dump.
     */
    public function up(): void
    {
        if (Schema::hasTable('whatsapp_accounts')) {
            return;
        }

        Schema::create('whatsapp_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('label')->nullable();

            $table->string('phone_number_id')->unique();
            $table->string('waba_id')->nullable();
            $table->string('display_phone_number')->nullable();
            $table->string('meta_business_id')->nullable();

            // Encrypted casts produce long ciphertext, so these are TEXT.
            $table->text('access_token');
            $table->text('app_secret')->nullable();

            // Per-account webhook verify token, replacing the old hardcoded one.
            $table->string('verify_token');

            $table->string('provider')->default('manual');            // manual|embedded
            $table->string('connection_status')->default('pending');  // pending|connected|failed|disabled
            $table->string('webhook_status')->default('unverified');  // unverified|verified

            $table->boolean('is_default')->default(false);
            $table->timestamp('last_verified_at')->nullable();
            $table->text('last_error')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('connection_status');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_accounts');
    }
};
