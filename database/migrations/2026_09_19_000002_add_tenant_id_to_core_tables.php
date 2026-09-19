<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tenant-owned tables that gain a nullable tenant_id foreign key.
     * Nullable so the additive migration is safe against the existing
     * (dump-loaded) database; a default tenant is created and rows are
     * backfilled below.
     */
    private array $tables = [
        'users',
        'appointments',
        'broadcast_messages',
        'doctor_service',
        'doctor_timings',
        'services',
        'sms_balance',
        'sms_logs',
        'sms_payments',
        'message_plans',
        'message_prices',
        'wallet_balance',
        'wallet_payments',
        'payments',
        'chat_sessions',
        'user_info',
    ];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && !Schema::hasColumn($name, 'tenant_id')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->after('id')->index();
                });
            }
        }

        $this->backfillDefaultTenant();
    }

    /**
     * Ensure a default "Tenant #1" exists and every legacy row is attached to
     * it, so the existing single-tenant data keeps working after tenancy is on.
     */
    private function backfillDefaultTenant(): void
    {
        if (!Schema::hasTable('tenants')) {
            return;
        }

        $tenantId = DB::table('tenants')->value('id');
        if (!$tenantId) {
            $tenantId = DB::table('tenants')->insertGetId([
                'name' => 'Default Tenant',
                'slug' => 'default',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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
