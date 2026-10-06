<x-app-layout>
    <script>
        if (window.innerWidth >= 640 && !window.location.search.includes('mobile_preview')) {
            window.location.replace("{{ route('dashboard') }}");
        }
    </script>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-indigo-400 shadow-sm shrink-0">
                @else
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-sm border-2 border-white shrink-0">
                        {{ strtoupper(substr(Auth::user()->formatted_name, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <h2 class="font-bold text-sm sm:text-base text-gray-900 dark:text-white leading-tight truncate">
                        Hello, {{ Auth::user()->formatted_name }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">Select an application menu module to continue</p>
                </div>
            </div>

            <!-- Opposite Side (Right): Role / Guest Pill -->
            <span class="text-xs px-2.5 py-1 rounded-full font-bold shrink-0 shadow-2xs {{ Auth::user()->isCarOwner() ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' }}">
                {{ Auth::user()->isCarOwner() ? '🚗 Car Owner' : '👤 Guest' }}
            </span>
        </div>
    </x-slot>

    <div class="py-6 px-3 sm:px-6 lg:px-8 bg-slate-50/50 dark:bg-gray-900/50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Clean App Launcher Template Grid (3x3 matching UI design) -->
            <div class="grid grid-cols-3 gap-2.5 sm:gap-7 md:gap-8">
                <!-- 6. Dashboard Analytics -->
                <a href="{{ route('dashboard') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-cyan-500/25 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition leading-tight sm:leading-snug max-w-full px-1">
                        Dashboard
                    </span>
                </a>
                <!-- 1. My Fleet -->
                <a href="{{ route('vehicles.index') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-md sm:shadow-lg shadow-blue-500/25 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 9l2-4h14l2 4M3 9v7a1 1 0 001 1h1m16-8v7a1 1 0 01-1 1h-1M3 9h18"/>
                            </svg>
                        </div>
                    </div>
                    <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition leading-tight sm:leading-snug max-w-full px-1">
                        My Fleet
                    </span>
                </a>
                <!-- All Listing -->
                <a href="{{ route('vehicles.all') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-emerald-500/25 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition leading-tight sm:leading-snug max-w-full px-1">
                        All Listing
                    </span>
                </a>
                <!-- 3. Bookings -->
                <a href="{{ route('bookings.index') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-purple-500/25 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        @if($activeBookings > 0)
                            <span class="absolute -top-1 -right-1 px-1.5 sm:px-2 py-0.5 bg-indigo-600 text-white text-[9px] sm:text-[11px] font-black rounded-full shadow-sm">
                                {{ $activeBookings }}
                            </span>
                        @endif
                    </div>
                    <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition leading-tight sm:leading-snug max-w-full px-1">
                        My Bookings
                    </span>
                </a>
                <!-- 4. Schedule View -->
                <a href="{{ route('calendar.index') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-amber-500/25 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition leading-tight sm:leading-snug max-w-full px-1">
                        My Calendar
                    </span>
                </a>
                <!-- My Income -->
                <a href="{{ route('income.index') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-emerald-500/25 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition leading-tight sm:leading-snug max-w-full px-1">
                        My Income
                    </span>
                </a>
                @if(Auth::user()->isAdmin())
                    <!-- 2. Destinations & Rates -->
                    <a href="{{ route('destinations.index') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                        <div class="relative">
                            <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-red-500 to-rose-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-rose-500/25 group-hover:scale-105 transition duration-200">
                                <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                        <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition leading-tight sm:leading-snug max-w-full px-1">
                            Rates
                        </span>
                    </a>

                    <!-- 7. User Approvals -->
                    <a href="{{ route('admin.users.index') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                        <div class="relative">
                            <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-pink-500 to-rose-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-pink-500/25 group-hover:scale-105 transition duration-200">
                                <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            @if($pendingApprovalsCount > 0)
                                <span class="absolute -top-1 -right-1 px-1.5 sm:px-2 py-0.5 bg-rose-500 text-white text-[9px] sm:text-[11px] font-black rounded-full shadow-sm animate-pulse">
                                    {{ $pendingApprovalsCount }}
                                </span>
                            @endif
                        </div>
                        <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition leading-tight sm:leading-snug max-w-full px-1">
                            Users
                        </span>
                    </a>
                @endif

                <!-- 8. My Profile -->
                <a href="{{ route('profile.edit') }}" class="m-1 sm:m-0 group relative bg-white dark:bg-gray-800 rounded-2xl sm:rounded-[28px] p-2.5 sm:p-7 py-3 sm:py-7 shadow-xs hover:shadow-md border border-slate-100 dark:border-gray-700/60 transition-all duration-200 transform hover:-translate-y-1 flex flex-col items-center justify-center text-center aspect-square sm:aspect-[4/3] min-h-[105px] sm:min-h-[190px]">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-20 sm:h-20 rounded-xl sm:rounded-[26px] bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center shadow-md sm:shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition duration-200">
                            <svg class="w-5 h-5 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <span class="mt-2 sm:mt-5 font-bold text-xs sm:text-base text-gray-800 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition leading-tight sm:leading-snug max-w-full px-1">
                        My Profile
                    </span>
                </a>
            </div>

            </div>

            <!-- Quick Fleet & Booking Status Card -->
            <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-xs border border-slate-100 dark:border-gray-700 grid grid-cols-3 gap-4 text-center">
                <div>
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider block">Available Cars</span>
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ $availableVehicles }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider block">Total Fleet</span>
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-0.5 block">{{ $totalVehicles }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider block">Active Trips</span>
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5 block">{{ $activeBookings }}</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
