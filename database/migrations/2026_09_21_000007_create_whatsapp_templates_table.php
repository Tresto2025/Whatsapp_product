<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A mirror of the tenant's Meta message templates.
     *
     * Templates are authored and approved on Meta, never here. We mirror them
     * so flows and campaigns can be built against a known-approved list, and
     * so a send is never attempted against a rejected or paused template.
     */
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_account_id')->constrained()->cascadeOnDelete();
            $table->string('meta_template_id')->nullable();
            $table->string('name');
            $table->string('language', 16)->default('en');
            $table->string('category')->nullable();      // utility|marketing|authentication
            $table->string('status')->default('pending'); // approved|pending|rejected|paused
            $table->string('header_type')->nullable();    // none|text|image|video|document
            $table->text('body')->nullable();
            $table->text('footer')->nullable();
            $table->json('buttons')->nullable();
            $table->json('variables')->nullable();        // positional {{1}}, {{2}}…
            $table->text('rejection_reason')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            // Meta allows one template per (name, language) per WABA.
            $table->unique(['whatsapp_account_id', 'name', 'language']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
