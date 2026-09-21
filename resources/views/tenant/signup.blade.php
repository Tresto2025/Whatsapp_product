<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">{{ __('Create your workspace') }}</h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Your business gets its own workspace, its own WhatsApp number and its own data. You will connect your Meta credentials in the next step.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('tenant.signup.store') }}">
        @csrf

        <div>
            <x-input-label for="business_name" :value="__('Business name')" />
            <x-text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name"
                          :value="old('business_name')" required autofocus />
            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="first_name" :value="__('Your first name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name"
                          :value="old('first_name')" required />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Your last name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name"
                          :value="old('last_name')" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Work email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone (optional)')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                          name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Already have a workspace?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Create workspace') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
