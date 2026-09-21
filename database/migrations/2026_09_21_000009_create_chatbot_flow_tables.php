<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The no-code chatbot: a flow is a set of triggers and an ordered tree of
     * steps. flow_runs holds one contact's live position through one flow.
     */
    public function up(): void
    {
        Schema::create('chatbot_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('default_reply')->nullable();   // when nothing matches
            $table->unsignedInteger('priority')->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('flow_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flow_id')->constrained('chatbot_flows')->cascadeOnDelete();
            $table->string('match_type');                 // keyword|button|template_reply|any
            $table->string('value')->nullable();
            $table->boolean('exact_match')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'match_type']);
        });

        Schema::create('flow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flow_id')->constrained('chatbot_flows')->cascadeOnDelete();
            $table->foreignId('parent_step_id')->nullable()->constrained('flow_steps')->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->string('action_type');                // send_text|send_template|ask_question|
                                                          // save_attribute|add_tag|record_response|
                                                          // handoff|end
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['flow_id', 'order']);
        });

        Schema::create('flow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flow_id')->constrained('chatbot_flows')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('current_step_id')->nullable()->constrained('flow_steps')->nullOnDelete();
            $table->json('state')->nullable();            // answers collected so far
            $table->string('status')->default('active');  // active|completed|abandoned|handed_off
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['contact_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flow_runs');
        Schema::dropIfExists('flow_steps');
        Schema::dropIfExists('flow_triggers');
        Schema::dropIfExists('chatbot_flows');
    }
};
