<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The transcript. Meta keeps no history, so this is the record the tenant
     * is really buying, and it is what every analytic is derived from.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_account_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('open');   // open|snoozed|closed
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'last_message_at']);
            $table->unique(['contact_id', 'whatsapp_account_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->string('direction', 3);              // in|out
            $table->string('type')->default('text');     // text|image|template|interactive|…
            $table->text('body')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->json('payload')->nullable();         // the raw Meta object
            $table->string('meta_message_id')->nullable();
            $table->string('status')->default('queued'); // queued|sent|delivered|read|failed
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Delivery receipts arrive later on their own webhook and are
            // matched back to the message by this id, so it must be indexed.
            $table->index('meta_message_id');
            $table->index(['tenant_id', 'created_at']);
            $table->index(['conversation_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
