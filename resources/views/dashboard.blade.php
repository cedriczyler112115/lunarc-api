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

            <!-- Section 1: My Fleet Availability Controls (Top of Dashboard) -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-200/80 dark:border-gray-700/80 pb-3">
                    <div>
                        <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 9l2-4h14l2 4M3 9v7a1 1 0 001 1h1m16-8v7a1 1 0 01-1 1h-1M3 9h18" />
                            </svg>
                            <span>{{ __('My Fleet Availability') }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300">
                                {{ $myVehicles->count() }} {{ Str::plural('Car', $myVehicles->count()) }}
                            </span>
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Quickly turn vehicle availability ON or OFF in real-time.</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-xs transition duration-150">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Vehicle
                        </a>
                        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-3.5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-xl border border-gray-200 dark:border-gray-700 transition duration-150">
                            Manage Fleet &rarr;
                        </a>
                    </div>
                </div>

                @if($myVehicles->count() > 0)
                    <!-- Medium Size Vehicle Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                        @foreach($myVehicles as $v)
                            @php
                                $imgSrc = $v->image_path
                                    ? (str_starts_with($v->image_path, 'http') || str_starts_with($v->image_path, '/') ? $v->image_path : '/' . $v->image_path)
                                    : '/images/placeholder.jpg';
                            @endphp
                            <div x-data="{
                                isAvailable: {{ $v->status === 'available' ? 'true' : 'false' }},
                                loading: false,
                                async toggle() {
                                    if (this.loading) return;
                                    this.loading = true;
                                    const nextState = !this.isAvailable;
                                    try {
                                        const res = await fetch('{{ route('vehicles.toggle-availability', $v->id) }}', {
                                            method: 'PATCH',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({ status: nextState ? 'available' : 'out_of_service' })
                                        });
                                        const data = await res.json();
                                        if (res.ok && data.success) {
                                            this.isAvailable = data.is_available;
                                        } else {
                                            alert(data.error || data.message || 'Failed to update vehicle availability');
                                        }
                                    } catch (e) {
                                        console.error(e);
                                        alert('Network or server error updating vehicle status.');
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/70 dark:border-gray-700/80 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between group">
                                
                                <div>
                                    <!-- Picture & Status Badges -->
                                    <div class="h-36 sm:h-40 w-full relative overflow-hidden bg-gray-100 dark:bg-gray-900/60">
                                        <img src="{{ $imgSrc }}" alt="{{ $v->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        
                                        <!-- Availability Indicator Badge -->
                                        <div class="absolute top-2.5 left-2.5 z-10">
                                            <span x-show="isAvailable" class="px-2.5 py-1 rounded-lg text-[10px] font-black tracking-wider uppercase bg-emerald-600/90 backdrop-blur-xs text-white shadow-xs flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                                Available
                                            </span>
                                            <span x-show="!isAvailable" style="display: none;" class="px-2.5 py-1 rounded-lg text-[10px] font-black tracking-wider uppercase bg-rose-600/90 backdrop-blur-xs text-white shadow-xs flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white/70"></span>
                                                Off-Duty
                                            </span>
                                        </div>

                                        <!-- License Plate Badge -->
                                        <div class="absolute top-2.5 right-2.5 z-10">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-black bg-black/80 backdrop-blur-xs text-white shadow-xs border border-white/20">
                                                {{ $v->license_plate }}
                                            </span>
                                        </div>

                                        <!-- Price Tag Overlay -->
                                        <div class="absolute bottom-2 left-2 z-10">
                                            <span class="px-2 py-1 rounded-lg text-xs font-black bg-slate-900/85 backdrop-blur-xs text-emerald-400 border border-white/10 shadow-xs">
                                                ₱{{ number_format($v->daily_rate, 0) }} <span class="text-[10px] font-normal text-slate-300">/day</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Vehicle Details -->
                                    <div class="p-3.5 sm:p-4 space-y-2.5">
                                        <div>
                                            <div class="flex items-center justify-between gap-1">
                                                <h4 class="font-extrabold text-sm sm:text-base text-gray-900 dark:text-white truncate" title="{{ $v->name }}">
                                                    {{ $v->name }}
                                                </h4>
                                                <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 shrink-0">
                                                    {{ $v->year }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                {{ $v->vehicleType->name ?? $v->make }} {{ $v->model ? '· ' . $v->model : '' }}
                                            </p>
                                        </div>

                                        <!-- Specs Tags -->
                                        <div class="flex flex-wrap items-center gap-1.5 text-[10px]">
                                            @if($v->transmission)
                                                <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700/70 text-gray-700 dark:text-gray-300 font-semibold flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                                    {{ $v->transmission }}
                                                </span>
                                            @endif
                                            @if($v->fuel_type)
                                                <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700/70 text-gray-700 dark:text-gray-300 font-semibold flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                    {{ $v->fuel_type }}
                                                </span>
                                            @endif
                                            @if($v->seats)
                                                <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700/70 text-gray-700 dark:text-gray-300 font-semibold flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                    {{ $v->seats }} Seats
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Footer: On/Off Availability Switch -->
                                <div class="px-3.5 sm:px-4 py-3 bg-gray-50/70 dark:bg-gray-900/40 border-t border-gray-100 dark:border-gray-700/80 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold" :class="isAvailable ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'">
                                            <span x-text="isAvailable ? 'Active & Ready' : 'Turned OFF'"></span>
                                        </span>
                                    </div>

                                    <!-- On/Off Switch Button -->
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="toggle()" :disabled="loading"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                                            :class="isAvailable ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'"
                                            role="switch" :aria-checked="isAvailable"
                                            :title="isAvailable ? 'Click to turn OFF (Unavailable)' : 'Click to turn ON (Available)'">
                                            <span class="sr-only">Toggle availability</span>
                                            <span aria-hidden="true"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                                                :class="isAvailable ? 'translate-x-5' : 'translate-x-0'">
                                                <svg x-show="loading" class="w-3 h-3 text-gray-500 animate-spin m-1" fill="none" viewBox="0 0 24 24" style="display: none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                            </span>
                                        </button>

                                        <!-- Edit Action Link -->
                                        <a href="{{ route('vehicles.edit', $v->id) }}" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition" title="Edit Vehicle Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State for User with No Vehicles -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-700 text-center space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-gray-200 text-base">No vehicles listed under your account</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">Register your car fleet to manage live availability, track bookings, and dispatch rentals.</p>
                        <div class="pt-1">
                            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                + Register Your First Vehicle
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            
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
