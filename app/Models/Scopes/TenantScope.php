<?php

namespace App\Models\Scopes;

use App\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that constrains queries on tenant-owned models to the current
 * tenant. No-ops when there is no tenant context or the context is bypassed
 * (super admin, console commands, queued jobs before a tenant is resolved).
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $manager = app(TenantManager::class);

        if (!$manager->shouldScope()) {
            return;
        }

        $builder->where(
            $model->getTable() . '.' . $model->getTenantColumn(),
            $manager->id()
        );
    }
}
