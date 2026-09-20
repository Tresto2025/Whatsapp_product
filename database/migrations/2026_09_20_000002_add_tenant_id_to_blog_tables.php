<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Closes a gap left by Phase 1: the blog tables are tenant-owned per the
     * architecture plan but were not given a tenant_id, so every tenant would
     * have shared the same posts and categories.
     *
     * Same shape as the Phase 1 migration: nullable + backfilled to the default
     * tenant so it is safe against the legacy database.
     */
    private array $tables = ['posts', 'categories'];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && !Schema::hasColumn($name, 'tenant_id')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id')->index();
                });
            }
        }

        if (!Schema::hasTable('tenants')) {
            return;
        }

        $tenantId = DB::table('tenants')->value('id');
        if (!$tenantId) {
            return;
        }

        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'tenant_id')) {
                DB::table($name)->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'tenant_id')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
