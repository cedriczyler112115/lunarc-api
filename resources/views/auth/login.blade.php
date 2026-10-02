<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-gray-900 dark:text-white">Welcome to LunarC Fleet</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Log in to manage vehicles, bookings & Mindanao trip schedules</p>
    </div>

    <!-- Session Status / Registration Pending Notice -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 rounded-r-xl shadow-sm text-xs font-semibold leading-relaxed">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="e.g. user@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-xs font-semibold text-gray-600 dark:text-gray-400">{{ __('Remember me on this device') }}</span>
            </label>
        </div>

        <!-- Submit Log in Button -->
        <div class="pt-2">
            <x-primary-button class="w-full py-3 justify-center text-sm font-extrabold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Registration Button & Section -->
    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 text-center space-y-3">
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
            Don't have an account yet?
        </p>
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md transition duration-150 gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Register New Account
        </a>
        <p class="text-[10px] text-gray-400 dark:text-gray-500 italic">
            Note: Newly registered accounts require Admin approval before access is enabled.
        </p>
    </div>
</x-guest-layout>
