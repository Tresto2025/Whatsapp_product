<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * What a conversation produced: the generic replacement for the old
     * `appointments` table.
     *
     * A clinic's flow records type=booking with scheduled_for; a restaurant's
     * records type=booking with a party size in `data`; a gym's "interested in
     * a trial" records type=interest. Everything a tenant asked to see — who
     * booked, who replied, who is interested in which service — is a query
     * over this table joined to contacts, so a new vertical needs no schema
     * change.
     */
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('flow_id')->nullable()->constrained('chatbot_flows')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');                        // booking|enquiry|interest|lead|custom
            $table->string('subject')->nullable();         // the service/meal/item asked about
            $table->string('status')->default('new');      // new|confirmed|cancelled|done
            $table->timestamp('scheduled_for')->nullable();
            $table->json('data')->nullable();              // the answers the flow collected
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'status']);
            $table->index(['tenant_id', 'scheduled_for']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
