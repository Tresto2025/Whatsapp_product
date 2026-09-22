<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A person a tenant talks to. Meta gives us only a phone number on each
     * inbound message, so everything else a tenant needs for segmentation and
     * targeting is kept here.
     *
     * Unique on (tenant_id, wa_id): the same person messaging two tenants is
     * two contacts, and neither tenant may see the other's row.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('wa_id', 32);                 // E.164 without '+', as Meta sends it
            $table->string('name')->nullable();          // set by the tenant
            $table->string('profile_name')->nullable();  // as WhatsApp reports it
            $table->string('email')->nullable();
            $table->string('locale', 16)->nullable();
            $table->json('attributes')->nullable();      // per-vertical fields live here
            $table->timestamp('opted_in_at')->nullable();
            $table->timestamp('opted_out_at')->nullable();
            $table->timestamp('last_inbound_at')->nullable();
            $table->timestamp('last_outbound_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'wa_id']);
            $table->index(['tenant_id', 'last_inbound_at']);
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('colour', 16)->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('contact_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['contact_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('contacts');
    }
};
