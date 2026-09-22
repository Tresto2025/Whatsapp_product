<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Apply to any Eloquent model whose table has a tenant_id column. Adds the
 * global TenantScope (read isolation) and auto-fills tenant_id on create
 * (write isolation) from the current tenant context.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            $column = $model->getTenantColumn();

            if (empty($model->{$column})) {
                $tenantId = app(TenantManager::class)->id();
                if ($tenantId) {
                    $model->{$column} = $tenantId;
                }
            }
        });
    }

    public function getTenantColumn(): string
    {
        return 'tenant_id';
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, $this->getTenantColumn());
    }
}
