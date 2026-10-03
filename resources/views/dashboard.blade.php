<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">LunarC Car Rental - Butuan City Fleet & Booking System</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Vehicle Data Entry
                </a>
                <a href="{{ route('bookings.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-lg shadow transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    New Car Booking
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Key Performance Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Vehicles -->
                <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Fleet</p>
                        <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $totalVehicles }}</h3>
                        <p class="text-xs text-indigo-500 font-medium mt-1">Vehicles in system</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                </div>

                <!-- Available Vehicles -->
                <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Available Cars</p>
                        <h3 class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $availableVehicles }}</h3>
                        <p class="text-xs text-emerald-500 font-medium mt-1">Ready to book</p>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Active Bookings -->
                <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Bookings</p>
                        <h3 class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $activeBookings }}</h3>
                        <p class="text-xs text-amber-500 font-medium mt-1">Confirmed / Pending</p>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Estimated Revenue</p>
                        <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">₱{{ number_format($totalRevenue, 2) }}</h3>
                        <p class="text-xs text-indigo-500 font-medium mt-1">From active & completed</p>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('vehicles.index') }}" class="group bg-gradient-to-r from-blue-600 to-indigo-600 p-4 sm:p-6 rounded-2xl text-white shadow-lg hover:shadow-xl transition duration-200 flex items-center justify-between">
                    <div>
                        <h4 class="text-xl font-bold">Vehicle Data Entry</h4>
                        <p class="text-blue-100 text-sm mt-1">Manage fleet specs, daily rates & statuses</p>
                    </div>
                    <div class="p-3 bg-white/20 group-hover:bg-white/30 rounded-xl backdrop-blur-sm transition duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </a>

                <a href="{{ route('calendar.index') }}" class="group bg-gradient-to-r from-amber-500 to-orange-600 p-6 rounded-2xl text-white shadow-lg hover:shadow-xl transition duration-200 flex items-center justify-between">
                    <div>
                        <h4 class="text-xl font-bold">Booking Calendar</h4>
                        <p class="text-amber-100 text-sm mt-1">Visual vehicle schedule across specific days</p>
                    </div>
                    <div class="p-3 bg-white/20 group-hover:bg-white/30 rounded-xl backdrop-blur-sm transition duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </a>

                <a href="{{ route('bookings.index') }}" class="group bg-gradient-to-r from-emerald-600 to-teal-600 p-6 rounded-2xl text-white shadow-lg hover:shadow-xl transition duration-200 flex items-center justify-between">
                    <div>
                        <h4 class="text-xl font-bold">Manage Bookings</h4>
                        <p class="text-emerald-100 text-sm mt-1">View customer reservations & statuses</p>
                    </div>
                    <div class="p-3 bg-white/20 group-hover:bg-white/30 rounded-xl backdrop-blur-sm transition duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </a>
            </div>

            <!-- Fleet Overview & Recent Bookings -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Vehicle Fleet Preview -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Vehicle Fleet Entry</h3>
                        <a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">View All Vehicles &rarr;</a>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($vehicles as $vehicle)
                            <div class="py-3 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($vehicle->make, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $vehicle->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $vehicle->license_plate }} • {{ $vehicle->transmission }} • {{ $vehicle->seats }} Seats</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-extrabold text-gray-900 dark:text-white">₱{{ number_format($vehicle->daily_rate, 2) }}</span>
                                    <span class="text-xs text-gray-400 block">/ day</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Bookings preview -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Car Bookings</h3>
                        <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">View All Bookings &rarr;</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentBookings as $booking)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-between">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">{{ $booking->booking_code }}</span>
                                        <h5 class="font-bold text-sm text-gray-900 dark:text-white">{{ $booking->vehicle->name ?? 'Vehicle' }}</h5>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Customer: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $booking->customer_name }}</span> | 
                                        {{ \Carbon\Carbon::parse($booking->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }} ({{ $booking->total_days }} days)
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase {{ $booking->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $booking->status }}
                                    </span>
                                    <span class="block text-xs font-extrabold text-gray-900 dark:text-white mt-1">₱{{ number_format($booking->total_price, 2) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No bookings found yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
