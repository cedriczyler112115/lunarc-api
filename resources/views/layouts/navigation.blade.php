<nav x-data="{ open: false }"
    class="sticky top-0 z-50">
    <!-- Green accent top bar -->
    <div class="h-1 bg-gradient-to-r from-emerald-500 via-green-400 to-teal-500"></div>

    <!-- Primary Navigation Menu (Desktop) -->
    <div class="hidden sm:block bg-white dark:bg-gray-900 border-b border-gray-200/60 dark:border-gray-700/60 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-[4.5rem]">
                <div class="flex items-center gap-1">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center mr-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                            <x-application-logo class="h-11 w-auto max-w-[50px] object-contain drop-shadow-xs group-hover:scale-105 transition-transform duration-200" />
                            <div class="hidden lg:flex flex-col">
                                <span class="font-black text-sm text-gray-900 dark:text-white tracking-tight leading-none">ONEDRIVE</span>
                                <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 tracking-widest uppercase leading-none mt-0.5">WHEELS</span>
                            </div>
                        </a>
                    </div>

                    <!-- Divider -->
                    <div class="hidden lg:block w-px h-8 bg-gray-200 dark:bg-gray-700 mx-2"></div>

                    <!-- Navigation Links -->
                    <div class="flex items-center gap-0.5">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <!-- My Account Dropdown (Desktop Only) -->
                        @php
                            $isMyAccountActive = request()->routeIs('vehicles.index', 'vehicles.create', 'vehicles.edit', 'bookings.*', 'calendar.*');
                        @endphp
                        <x-dropdown align="left" width="w-56">
                            <x-slot name="trigger">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm leading-5 transition duration-200 ease-in-out cursor-pointer focus:outline-none {{ $isMyAccountActive ? 'font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/80 dark:border-emerald-800/60' : 'font-medium text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 border border-transparent' }}">
                                    <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>{{ __('My Account') }}</span>
                                    <svg class="w-3.5 h-3.5 ml-1 opacity-60 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Host & Operations Hub</p>
                                </div>

                                <div class="py-1">
                                    <!-- My Fleet -->
                                    <x-dropdown-link :href="route('vehicles.index')" class="{{ request()->routeIs('vehicles.index', 'vehicles.create', 'vehicles.edit') ? 'bg-emerald-50/80 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold' : '' }}">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 9l2-4h14l2 4M3 9v7a1 1 0 001 1h1m16-8v7a1 1 0 01-1 1h-1M3 9h18"/></svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-xs leading-none text-gray-800 dark:text-gray-200">{{ __('My Fleet') }}</div>
                                                <div class="text-[10px] text-gray-400 dark:text-gray-400 mt-0.5">Manage your vehicles</div>
                                            </div>
                                        </div>
                                    </x-dropdown-link>

                                    <!-- My Bookings -->
                                    <x-dropdown-link :href="route('bookings.index')" class="{{ request()->routeIs('bookings.index', 'bookings.show') ? 'bg-emerald-50/80 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold' : '' }}">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-xs leading-none text-gray-800 dark:text-gray-200">{{ __('My Bookings') }}</div>
                                                <div class="text-[10px] text-gray-400 dark:text-gray-400 mt-0.5">Rental reservations</div>
                                            </div>
                                        </div>
                                    </x-dropdown-link>

                                    <!-- My Calendar -->
                                    <x-dropdown-link :href="route('calendar.index')" class="{{ request()->routeIs('calendar.index') ? 'bg-emerald-50/80 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold' : '' }}">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-xs leading-none text-gray-800 dark:text-gray-200">{{ __('My Calendar') }}</div>
                                                <div class="text-[10px] text-gray-400 dark:text-gray-400 mt-0.5">Schedule & dispatch</div>
                                            </div>
                                        </div>
                                    </x-dropdown-link>
                                </div>
                            </x-slot>
                        </x-dropdown>

                        <x-nav-link :href="route('vehicles.all')" :active="request()->routeIs('vehicles.all')">
                            <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            {{ __('All Listing') }}
                        </x-nav-link>

                        @if(Auth::user()->isAdmin())
                            <x-nav-link :href="route('destinations.index')" :active="request()->routeIs('destinations.*')">
                                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ __('Rates') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                                {{ __('Users') }}
                                @php
                                    $pCount = \App\Models\User::where('is_approved', false)->count();
                                @endphp
                                @if($pCount > 0)
                                    <span class="ms-1.5 px-1.5 py-0.5 text-[10px] font-black rounded-full bg-amber-500 text-white shadow-sm shadow-amber-500/30 animate-pulse">{{ $pCount }}</span>
                                @endif
                            </x-nav-link>
                        @endif
                    </div>
                </div>

                <!-- Right: Dark Mode Toggle + User Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-4 gap-2">
                    <!-- Dark Mode Toggle (Desktop) -->
                    <button onclick="window.toggleDarkMode()" type="button"
                        class="relative w-10 h-10 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all duration-200 focus:outline-none"
                        title="Toggle Dark Mode">
                        <!-- Sun icon (visible in dark mode) -->
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <!-- Moon icon (visible in light mode) -->
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <!-- User Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="group inline-flex items-center gap-2.5 px-3 py-2 rounded-xl border border-gray-200/80 dark:border-gray-700/60 text-sm leading-4 font-semibold text-gray-700 dark:text-gray-300 bg-gray-50/50 dark:bg-gray-800/50 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 hover:border-emerald-200 dark:hover:border-emerald-800/60 focus:outline-none transition-all duration-200">
                                @if(Auth::user()->avatar_url)
                                    <img src="{{ Auth::user()->avatar_url }}"
                                        class="w-7 h-7 rounded-lg object-cover ring-2 ring-emerald-400/50 dark:ring-emerald-500/40 shadow-sm">
                                @else
                                    <div
                                        class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-green-600 dark:from-emerald-400 dark:to-green-500 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                        {{ strtoupper(substr(Auth::user()->formatted_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="font-bold text-gray-800 dark:text-gray-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors duration-200">{{ Auth::user()->formatted_name }}</div>

                                <svg class="fill-current h-4 w-4 text-gray-400 dark:text-gray-500 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Signed in as</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate mt-0.5">{{ Auth::user()->formatted_name }}</p>
                            </div>

                            <div class="py-1">
                                <x-dropdown-link :href="route('profile.edit')">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ __('Profile') }}
                                    </span>
                                </x-dropdown-link>
                            </div>

                            <div class="border-t border-gray-100 dark:border-gray-700 py-1">
                                <!-- Authentication -->
                                <form id="logout-form-desktop" method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="button" onclick="event.preventDefault(); window.confirmLogout(document.getElementById('logout-form-desktop'));"
                                        class="flex items-center gap-2 w-full px-4 py-2 text-start text-sm leading-5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 focus:outline-none transition duration-150 ease-in-out font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Mobile Header (Real Mobile App Bar) -->
    <div class="block sm:hidden bg-white dark:bg-gray-900 border-b border-gray-200/80 dark:border-gray-800 px-3 py-2.5 transition-colors duration-300 shadow-xs">
        <div class="flex items-center justify-between gap-2">
            <!-- Left: App Logo & Brand Name (Compact & Optimized) -->
            <a href="{{ route('menu') }}" class="flex items-center gap-2 shrink-0 group active:scale-95 transition-transform duration-150 min-w-0">
                <x-application-logo class="h-9 w-auto max-w-[38px] object-contain drop-shadow-xs group-hover:scale-105 transition-transform duration-200" />
                <div class="flex flex-col justify-center">
                    <span class="font-black text-sm text-gray-900 dark:text-white tracking-tight leading-none">ONEDRIVE</span>
                    <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 tracking-wider uppercase leading-none mt-0.5">WHEELS</span>
                </div>
            </a>

            <!-- Right: Theme Toggle, Home & Logout (Compact Touch Targets) -->
            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Dark Mode Toggle (Mobile) -->
                <button onclick="window.toggleDarkMode()" type="button"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-emerald-600 dark:hover:text-emerald-400 active:scale-90 transition-all duration-150 border border-gray-200/80 dark:border-gray-700/80"
                    title="Toggle Dark Mode">
                    <svg class="w-3.5 h-3.5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg class="w-3.5 h-3.5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- Home Button -->
                <a href="{{ route('menu') }}"
                    class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/70 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 px-2.5 py-1.5 rounded-lg border border-emerald-300/80 dark:border-emerald-800/80 active:scale-90 transition-all duration-150 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('Home') }}</span>
                </a>

                <!-- Logout Button -->
                <form id="logout-form-mobile" method="POST" action="{{ route('logout') }}" class="inline-flex items-center">
                    @csrf
                    <button type="button" onclick="event.preventDefault(); window.confirmLogout(document.getElementById('logout-form-mobile'));"
                        class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 bg-rose-50 dark:bg-rose-950/70 hover:bg-rose-100 dark:hover:bg-rose-900/50 px-2.5 py-1.5 rounded-lg border border-rose-300/80 dark:border-rose-800/80 active:scale-90 transition-all duration-150 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>{{ __('Log Out') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Fixed Mobile App Bottom Tab Bar (Real App Navigation) -->
<div class="fixed bottom-0 left-0 right-0 z-50 sm:hidden transition-colors duration-300 select-none">
    <div class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-2 py-2 pb-2.5 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] dark:shadow-[0_-4px_25px_rgba(0,0,0,0.5)]">
        <div class="grid grid-cols-5 text-center gap-1 items-center">
            <!-- 1. Home -->
            <a href="{{ route('menu') }}"
                class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl active:scale-90 transition-all duration-150 {{ request()->routeIs('menu') ? 'text-emerald-700 dark:text-emerald-300 font-black bg-emerald-50 dark:bg-emerald-950/70 shadow-xs ring-1 ring-emerald-500/20' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-bold' }}">
                <svg class="w-6 h-6 mb-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs leading-tight tracking-tight">Home</span>
            </a>

            <!-- 2. My Fleet -->
            <a href="{{ route('vehicles.index') }}"
                class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl active:scale-90 transition-all duration-150 {{ request()->routeIs('vehicles.*') ? 'text-emerald-700 dark:text-emerald-300 font-black bg-emerald-50 dark:bg-emerald-950/70 shadow-xs ring-1 ring-emerald-500/20' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-bold' }}">
                <svg class="w-6 h-6 mb-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 9l2-4h14l2 4M3 9v7a1 1 0 001 1h1m16-8v7a1 1 0 01-1 1h-1M3 9h18" />
                </svg>
                <span class="text-xs leading-tight tracking-tight truncate max-w-full">My Fleet</span>
            </a>

            <!-- 3. My Bookings -->
            <a href="{{ route('bookings.index') }}"
                class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl active:scale-90 transition-all duration-150 {{ request()->routeIs('bookings.*') ? 'text-emerald-700 dark:text-emerald-300 font-black bg-emerald-50 dark:bg-emerald-950/70 shadow-xs ring-1 ring-emerald-500/20' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-bold' }}">
                <svg class="w-6 h-6 mb-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="text-xs leading-tight tracking-tight truncate max-w-full">Bookings</span>
            </a>

            <!-- 4. Calendar -->
            <a href="{{ route('calendar.index') }}"
                class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl active:scale-90 transition-all duration-150 {{ request()->routeIs('calendar.*') ? 'text-emerald-700 dark:text-emerald-300 font-black bg-emerald-50 dark:bg-emerald-950/70 shadow-xs ring-1 ring-emerald-500/20' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-bold' }}">
                <svg class="w-6 h-6 mb-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs leading-tight tracking-tight">Calendar</span>
            </a>

            <!-- 5. Profile -->
            <a href="{{ route('profile.edit') }}"
                class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl active:scale-90 transition-all duration-150 {{ request()->routeIs('profile.*') ? 'text-emerald-700 dark:text-emerald-300 font-black bg-emerald-50 dark:bg-emerald-950/70 shadow-xs ring-1 ring-emerald-500/20' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-bold' }}">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}"
                        class="w-6 h-6 mb-1 shrink-0 rounded-full object-cover border-2 {{ request()->routeIs('profile.*') ? 'border-emerald-600 dark:border-emerald-400 ring-2 ring-emerald-400/30' : 'border-gray-400 dark:border-gray-500' }}">
                @else
                    <div
                        class="w-6 h-6 mb-1 shrink-0 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 dark:from-emerald-400 dark:to-green-500 text-white font-black text-[11px] flex items-center justify-center border-2 {{ request()->routeIs('profile.*') ? 'border-emerald-600 dark:border-emerald-400 ring-2 ring-emerald-400/30' : 'border-gray-400 dark:border-gray-500' }}">
                        {{ strtoupper(substr(Auth::user()->formatted_name, 0, 1)) }}
                    </div>
                @endif
                <span class="text-xs leading-tight tracking-tight">Profile</span>
            </a>
        </div>
    </div>
</div>