<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Self-serve signup for a business that wants its own workspace.
 *
 * This is deliberately separate from the doctor registration wizard: that one
 * adds a practitioner to an existing clinic, whereas this creates the tenant
 * itself and makes the registrant its admin. Without it a new signup landed on
 * a user with no tenant_id, which ResolveTenant correctly refuses — so there
 * was no way to become a tenant short of seeding the database.
 */
class TenantSignupController extends Controller
{
    public function create(): View
    {
        return view('tenant.signup');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // The tenant and its first admin only make sense together.
        $user = DB::transaction(function () use ($data) {
            $tenant = Tenant::create([
                'name' => $data['business_name'],
                'slug' => $this->uniqueSlug($data['business_name']),
                'status' => 'active',
                'contact_email' => $data['email'],
                'contact_phone' => $data['phone'] ?? null,
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_TENANT_ADMIN,
                'status' => 1,
            ]);

            $tenant->forceFill(['owner_user_id' => $user->id])->save();

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        // Put the tenant context in place for the redirect target, which is
        // tenant-scoped.
        app(TenantManager::class)->set($user->tenant);

        return redirect()
            ->route('tenant.whatsapp.create')
            ->with('success', 'Workspace created. Connect your WhatsApp number to go live.');
    }

    /**
     * Slugs are unique across the platform, so a second "Northside Clinic"
     * gets northside-clinic-2 rather than failing on the unique index.
     */
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'workspace';
        $slug = $base;
        $n = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$n);
        }

        return $slug;
    }
}
