<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-gray-900 dark:text-white">Register Account</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Create your user account for LunarC Car Rental System</p>
    </div>

    <!-- Admin Approval Notice Banner -->
    <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-900 dark:bg-amber-950/40 dark:text-amber-200 rounded-r-xl text-xs space-y-1">
        <div class="font-extrabold uppercase tracking-wider flex items-center gap-1.5 text-amber-700 dark:text-amber-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Admin Approval Required</span>
        </div>
        <p class="leading-relaxed">
            All new user registrations must be reviewed and approved by an Administrator before login credentials become active.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="e.g. Maria Santos" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="e.g. maria@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-4">
            <a class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('login') }}">
                {{ __('Already registered? Log in here') }}
            </a>

            <x-primary-button class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl shadow-md text-xs font-extrabold uppercase">
                {{ __('Submit Registration') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
