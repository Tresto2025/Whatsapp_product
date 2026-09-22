<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Plans/subscriptions for the software, plus the daily rollups analytics
     * reads from.
     *
     * Note Meta bills the tenant directly for conversations; what is metered
     * here is platform usage, not WhatsApp's own charges.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('price_minor')->default(0);   // in the smallest currency unit
            $table->string('currency', 3)->default('INR');
            $table->string('interval')->default('month');         // month|year
            $table->unsignedInteger('message_limit')->nullable(); // null = unlimited
            $table->unsignedInteger('contact_limit')->nullable();
            $table->unsignedInteger('user_limit')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('active');  // active|past_due|cancelled|trialing
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('gateway')->nullable();        // razorpay|manual
            $table->string('gateway_reference')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('messages_in')->default(0);
            $table->unsignedInteger('messages_out')->default(0);
            $table->unsignedInteger('messages_delivered')->default(0);
            $table->unsignedInteger('messages_read')->default(0);
            $table->unsignedInteger('messages_failed')->default(0);
            $table->unsignedInteger('new_contacts')->default(0);
            $table->unsignedInteger('conversations_started')->default(0);
            $table->unsignedInteger('responses_recorded')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
