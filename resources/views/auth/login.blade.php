<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">{{ __('Welcome back') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('Sign in to your workspace.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->has('role'))
        <div class="mb-4 text-sm text-red-600">{{ $errors->first('role') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                          required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center">
                <input id="remember" type="checkbox" class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('tenant.signup') }}">
                {{ __("Don't have a workspace?") }}
            </a>

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
