<nav x-data="{ open: false }"
    class="sticky top-0 z-50 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-100 dark:border-gray-700 shadow-xs">
    <!-- Primary Navigation Menu (Desktop) -->
    <div class="hidden sm:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('menu') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('menu')" :active="request()->routeIs('menu')">
                        📱 {{ __('App Menu') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.*')">
                        {{ __('Vehicles Entry') }}
                    </x-nav-link>
                    <x-nav-link :href="route('destinations.index')" :active="request()->routeIs('destinations.*')">
                        {{ __('Destinations & Rates') }}
                    </x-nav-link>
                    <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index', 'bookings.show')">
                        {{ __('Bookings') }}
                    </x-nav-link>
                    <x-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.index')">
                        {{ __('Booking Calendar') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('User Approvals') }}
                        @php
                            $pCount = \App\Models\User::where('is_approved', false)->count();
                        @endphp
                        @if($pCount > 0)
                            <span
                                class="ms-1.5 px-1.5 py-0.2 text-[10px] font-black rounded-full bg-amber-500 text-white">{{ $pCount }}</span>
                        @endif
                    </x-nav-link>
                </div>

            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            @if(Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}"
                                    class="w-6 h-6 rounded-full object-cover border border-indigo-300 dark:border-indigo-600 shadow-xs">
                            @else
                                <div
                                    class="w-6 h-6 rounded-full bg-indigo-600 text-white font-bold text-[10px] flex items-center justify-center shadow-xs">
                                    {{ strtoupper(substr(Auth::user()->formatted_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="font-bold">{{ Auth::user()->formatted_name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('menu')">
                            📱 {{ __('App Menu') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')">
                            👤 {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}"
                            onsubmit="return confirm('Are you sure you want to log out?');">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                if (confirm('Are you sure you want to log out?')) { this.closest('form').submit(); }">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Top Mobile Header (App Logo on Left, Home & Logout Buttons on Right) -->
    <div
        class="block sm:hidden border-b border-gray-100 dark:border-gray-700/80 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md px-4 py-2.5">
        <div class="flex items-center justify-between gap-2">
            <!-- Left: App Logo & Brand Name -->
            <a href="{{ route('menu') }}" class="flex items-center gap-2 shrink-0">
                <x-application-logo class="block h-6 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                <span class="font-black text-sm text-gray-900 dark:text-white tracking-tight uppercase">ONEDRIVE CAR
                    BOOKING</span>
            </a>

            <!-- Right: Home & Logout Menu Buttons -->
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('menu') }}"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 px-2.5 py-1 rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 transition duration-150 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('Home') }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}"
                    onsubmit="return confirm('Are you sure you want to log out?');" class="inline-flex items-center">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 px-2.5 py-1 rounded-lg border border-rose-200/80 dark:border-rose-800/80 transition duration-150 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>{{ __('Log Out') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Fixed Mobile App Bottom 5-Item Tab Menu Bar (Icons on top of label, fits without scrolling) -->
<div
    class="fixed bottom-0 left-0 right-0 z-50 sm:hidden bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-700/80 shadow-lg px-1 py-1.5">
    <div class="grid grid-cols-5 text-center">
        <!-- 1. Home -->
        <a href="{{ route('menu') }}"
            class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition duration-150 {{ request()->routeIs('menu') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] leading-tight tracking-tight">Home</span>
        </a>

        <!-- 2. My Vehicle -->
        <a href="{{ route('vehicles.index') }}"
            class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition duration-150 {{ request()->routeIs('vehicles.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 9l2-4h14l2 4M3 9v7a1 1 0 001 1h1m16-8v7a1 1 0 01-1 1h-1M3 9h18" />
            </svg>
            <span class="text-[10px] leading-tight tracking-tight truncate max-w-full">My Vehicle</span>
        </a>

        <!-- 3. My Bookings -->
        <a href="{{ route('bookings.index') }}"
            class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition duration-150 {{ request()->routeIs('bookings.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span class="text-[10px] leading-tight tracking-tight truncate max-w-full">Bookings</span>
        </a>

        <!-- 4. Calendar -->
        <a href="{{ route('calendar.index') }}"
            class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition duration-150 {{ request()->routeIs('calendar.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-[10px] leading-tight tracking-tight">Calendar</span>
        </a>

        <!-- 5. Profile -->
        <a href="{{ route('profile.edit') }}"
            class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition duration-150 {{ request()->routeIs('profile.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold bg-indigo-50/80 dark:bg-indigo-950/60' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium' }}">
            @if(Auth::user()->avatar_url)
                <img src="{{ Auth::user()->avatar_url }}"
                    class="w-5 h-5 mb-0.5 shrink-0 rounded-full object-cover border-2 {{ request()->routeIs('profile.*') ? 'border-indigo-600 dark:border-indigo-400' : 'border-gray-400 dark:border-gray-500' }}">
            @else
                <div
                    class="w-5 h-5 mb-0.5 shrink-0 rounded-full bg-indigo-600 text-white font-black text-[9px] flex items-center justify-center border-2 {{ request()->routeIs('profile.*') ? 'border-indigo-600 dark:border-indigo-400' : 'border-gray-400 dark:border-gray-500' }}">
                    {{ strtoupper(substr(Auth::user()->formatted_name, 0, 1)) }}
                </div>
            @endif
            <span class="text-[10px] leading-tight tracking-tight">Profile</span>
        </a>
    </div>
</div>