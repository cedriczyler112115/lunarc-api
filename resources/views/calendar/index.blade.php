<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('Vehicle Booking Calendar') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Integrated vehicle schedule & reservation calendar with status tracking and unique vehicle colors.</p>
            </div>
            <a href="{{ route('bookings.create', request()->only('vehicle_id')) }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Book Car for Selected Days
            </a>
        </div>
    </x-slot>

    @php
        $colorPalettes = [
            [
                'name' => 'Indigo',
                'bg' => 'bg-indigo-50 dark:bg-indigo-950/70',
                'border' => 'border-indigo-200 dark:border-indigo-700',
                'text' => 'text-indigo-900 dark:text-indigo-200',
                'badge' => 'bg-indigo-600 text-white',
                'dot' => 'bg-indigo-500',
                'pill' => 'bg-indigo-100 text-indigo-900 dark:bg-indigo-900/60 dark:text-indigo-200 border-indigo-200 dark:border-indigo-700',
            ],
            [
                'name' => 'Emerald',
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/70',
                'border' => 'border-emerald-200 dark:border-emerald-700',
                'text' => 'text-emerald-900 dark:text-emerald-200',
                'badge' => 'bg-emerald-600 text-white',
                'dot' => 'bg-emerald-500',
                'pill' => 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/60 dark:text-emerald-200 border-emerald-200 dark:border-emerald-700',
            ],
            [
                'name' => 'Amber',
                'bg' => 'bg-amber-50 dark:bg-amber-950/70',
                'border' => 'border-amber-200 dark:border-amber-700',
                'text' => 'text-amber-900 dark:text-amber-200',
                'badge' => 'bg-amber-600 text-white',
                'dot' => 'bg-amber-500',
                'pill' => 'bg-amber-100 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200 border-amber-200 dark:border-amber-700',
            ],
            [
                'name' => 'Rose',
                'bg' => 'bg-rose-50 dark:bg-rose-950/70',
                'border' => 'border-rose-200 dark:border-rose-700',
                'text' => 'text-rose-900 dark:text-rose-200',
                'badge' => 'bg-rose-600 text-white',
                'dot' => 'bg-rose-500',
                'pill' => 'bg-rose-100 text-rose-900 dark:bg-rose-900/60 dark:text-rose-200 border-rose-200 dark:border-rose-700',
            ],
            [
                'name' => 'Purple',
                'bg' => 'bg-purple-50 dark:bg-purple-950/70',
                'border' => 'border-purple-200 dark:border-purple-700',
                'text' => 'text-purple-900 dark:text-purple-200',
                'badge' => 'bg-purple-600 text-white',
                'dot' => 'bg-purple-500',
                'pill' => 'bg-purple-100 text-purple-900 dark:bg-purple-900/60 dark:text-purple-200 border-purple-200 dark:border-purple-700',
            ],
            [
                'name' => 'Cyan',
                'bg' => 'bg-cyan-50 dark:bg-cyan-950/70',
                'border' => 'border-cyan-200 dark:border-cyan-700',
                'text' => 'text-cyan-900 dark:text-cyan-200',
                'badge' => 'bg-cyan-600 text-white',
                'dot' => 'bg-cyan-500',
                'pill' => 'bg-cyan-100 text-cyan-900 dark:bg-cyan-900/60 dark:text-cyan-200 border-cyan-200 dark:border-cyan-700',
            ],
            [
                'name' => 'Fuchsia',
                'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/70',
                'border' => 'border-fuchsia-200 dark:border-fuchsia-700',
                'text' => 'text-fuchsia-900 dark:text-fuchsia-200',
                'badge' => 'bg-fuchsia-600 text-white',
                'dot' => 'bg-fuchsia-500',
                'pill' => 'bg-fuchsia-100 text-fuchsia-900 dark:bg-fuchsia-900/60 dark:text-fuchsia-200 border-fuchsia-200 dark:border-fuchsia-700',
            ],
        ];

        $vehicleColorMap = [];
        foreach ($allVehicles as $index => $v) {
            $vehicleColorMap[$v->id] = $colorPalettes[$index % count($colorPalettes)];
        }
    @endphp

    <div class="py-8" x-data="{ activeTab: 'grid' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Month Navigation & Vehicle Filter Header -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Month Navigator -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('calendar.index', ['month' => $prevMonth, 'vehicle_id' => $selectedVehicleId]) }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white min-w-[180px] text-center">
                        {{ $currentDate->format('F Y') }}
                    </h3>
                    <a href="{{ route('calendar.index', ['month' => $nextMonth, 'vehicle_id' => $selectedVehicleId]) }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('calendar.index', ['month' => date('Y-m'), 'vehicle_id' => $selectedVehicleId]) }}" class="px-3 py-1.5 text-xs font-bold bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-lg">
                        Today
                    </a>
                </div>

                <!-- Vehicle Filter & View Switcher -->
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <form method="GET" action="{{ route('calendar.index') }}" class="flex items-center space-x-2">
                        <input type="hidden" name="month" value="{{ $selectedMonth }}">
                        <select name="vehicle_id" onchange="this.form.submit()" class="text-xs font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-amber-500 p-2.5 min-w-[220px]">
                            <option value="">All Vehicles Fleet</option>
                            @foreach($allVehicles as $v)
                                <option value="{{ $v->id }}" {{ (string)$selectedVehicleId === (string)$v->id ? 'selected' : '' }}>
                                    {{ $v->name }} ({{ $v->license_plate }})
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <!-- View Switch Buttons -->
                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                        <button @click="activeTab = 'grid'" :class="activeTab === 'grid' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'" class="px-3 py-1.5 text-xs font-bold rounded-lg transition">
                            Month Calendar
                        </button>
                        <button @click="activeTab = 'timeline'" :class="activeTab === 'timeline' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'" class="px-3 py-1.5 text-xs font-bold rounded-lg transition">
                            Vehicle Timelines
                        </button>
                    </div>
                </div>

            </div>

            <!-- Distinct Vehicle Color Legend & Reservation Status Legend -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Vehicle Palette Legend -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-2">
                    <span class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider block">Vehicle Color Key Legend</span>
                    <div class="flex flex-wrap items-center gap-2.5 text-xs">
                        @foreach($allVehicles as $v)
                            @php
                                $pal = $vehicleColorMap[$v->id] ?? $colorPalettes[0];
                            @endphp
                            <a href="{{ route('calendar.index', ['month' => $selectedMonth, 'vehicle_id' => $v->id]) }}" class="inline-flex items-center space-x-2 px-3 py-1 rounded-xl border transition {{ (string)$selectedVehicleId === (string)$v->id ? 'ring-2 ring-indigo-500 font-extrabold' : '' }} {{ $pal['bg'] }} {{ $pal['border'] }} {{ $pal['text'] }}">
                                <span class="w-3 h-3 rounded-full {{ $pal['dot'] }} shadow-xs"></span>
                                <span class="font-bold">{{ $v->name }}</span>
                                <span class="text-[10px] opacity-75 font-mono">({{ $v->license_plate }})</span>
                            </a>
                        @endforeach
                        @if($selectedVehicleId)
                            <a href="{{ route('calendar.index', ['month' => $selectedMonth]) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-bold ml-2">Show All</a>
                        @endif
                    </div>
                </div>

                <!-- Booking Status Badge Legend -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-2">
                    <span class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider block">Booking Status Badges</span>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-xs">Confirmed</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-500 text-white shadow-xs">Pending</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-600 text-white shadow-xs">Completed</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs">Cancelled</span>
                    </div>
                </div>
            </div>

            <!-- TAB 1: Monthly Calendar Grid -->
            <div x-show="activeTab === 'grid'" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-center py-3 text-xs font-bold uppercase text-gray-500">
                    <div class="text-rose-500">Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                </div>

                <!-- Calendar Days Loop -->
                @php
                    $iterDate = $startOfWeek->copy();
                @endphp

                <div class="grid grid-cols-7 divide-x divide-y divide-gray-100 dark:divide-gray-700">
                    @while($iterDate <= $endOfWeek)
                        @php
                            $dateStr = $iterDate->format('Y-m-d');
                            $isCurrentMonth = $iterDate->month === $currentDate->month;
                            $isToday = $iterDate->isToday();
                            
                            // Get bookings covering this day
                            $dayBookings = $bookings->filter(function($b) use ($dateStr) {
                                return $dateStr >= $b->start_date->format('Y-m-d') && $dateStr <= $b->end_date->format('Y-m-d');
                            });
                        @endphp

                        <div class="min-h-[140px] p-2 transition hover:bg-gray-50/70 dark:hover:bg-gray-700/30 group relative flex flex-col justify-between {{ !$isCurrentMonth ? 'bg-gray-50/40 dark:bg-gray-900/40 text-gray-400' : '' }}">
                            <!-- Date Number & Quick Add Button -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold px-2 py-0.5 rounded-full {{ $isToday ? 'bg-indigo-600 text-white' : ($isCurrentMonth ? 'text-gray-900 dark:text-white' : 'text-gray-400') }}">
                                    {{ $iterDate->day }}
                                </span>
                                <a href="{{ route('bookings.create', ['start_date' => $dateStr, 'end_date' => $dateStr, 'vehicle_id' => $selectedVehicleId]) }}" title="Book on this date" class="opacity-0 group-hover:opacity-100 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded transition">
                                    + Book
                                </a>
                            </div>

                            <!-- Bookings List on this Day with Unique Vehicle Color & Status Badge -->
                            <div class="mt-1.5 space-y-1.5 overflow-y-auto max-h-[100px]">
                                @foreach($dayBookings as $bk)
                                    @php
                                        $pal = $vehicleColorMap[$bk->vehicle_id] ?? $colorPalettes[0];
                                    @endphp
                                    <a href="{{ route('bookings.show', $bk->id) }}" title="{{ $bk->vehicle->name ?? '' }} - {{ $bk->customer_name }} [Destination: {{ $bk->destination ?? 'N/A' }}] [Status: {{ strtoupper($bk->status) }}]" class="block p-1.5 rounded-xl text-[10px] leading-tight font-bold transition shadow-xs border {{ $pal['bg'] }} {{ $pal['border'] }} {{ $pal['text'] }} hover:scale-[1.02]">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="truncate font-extrabold">🚗 {{ $bk->vehicle->name ?? 'Vehicle' }}</span>
                                            <!-- Booking Status Badge -->
                                            <span class="px-1.5 py-0.2 text-[8px] font-black uppercase tracking-wider rounded text-white flex-shrink-0 {{ $bk->status === 'confirmed' ? 'bg-emerald-600' : ($bk->status === 'completed' ? 'bg-blue-600' : ($bk->status === 'pending' ? 'bg-amber-500' : 'bg-rose-600')) }}">
                                                {{ $bk->status }}
                                            </span>
                                        </div>
                                        <span class="block font-semibold text-[9px] opacity-80 mt-0.5 truncate">{{ $bk->customer_name }}</span>
                                        @if($bk->destination)
                                            <span class="block text-[8px] font-extrabold truncate text-sky-700 dark:text-sky-300 mt-0.5">📍 {{ $bk->destination }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        @php
                            $iterDate->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>

            <!-- TAB 2: Vehicle Timeline Schedule Grid -->
            <div x-show="activeTab === 'timeline'" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden p-6 space-y-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white border-b pb-3 border-gray-100 dark:border-gray-700">
                    Vehicle Fleet Schedule Matrix & Operational Statuses — {{ $currentDate->format('F Y') }}
                </h3>

                <div class="space-y-4">
                    @foreach($vehicles as $vehicle)
                        @php
                            $vBookings = $bookings->where('vehicle_id', $vehicle->id);
                            $pal = $vehicleColorMap[$vehicle->id] ?? $colorPalettes[0];
                        @endphp
                        <div class="p-4 rounded-2xl border space-y-3 transition {{ $pal['bg'] }} {{ $pal['border'] }}">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-1 text-xs font-black rounded-lg shadow-xs {{ $pal['badge'] }} font-mono">
                                        {{ $vehicle->license_plate }}
                                    </span>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-extrabold text-sm {{ $pal['text'] }}">{{ $vehicle->name }}</h4>
                                            <!-- Operational Fleet Status -->
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $vehicle->status === 'available' ? 'bg-emerald-500 text-white' : ($vehicle->status === 'maintenance' ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white') }}">
                                                {{ str_replace('_', ' ', $vehicle->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs opacity-75 {{ $pal['text'] }}">₱{{ number_format($vehicle->daily_rate, 2) }}/day • {{ $vehicle->transmission }} • {{ $vehicle->seats }} Seats</p>
                                    </div>
                                </div>
                                <a href="{{ route('bookings.create', ['vehicle_id' => $vehicle->id]) }}" class="px-3.5 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:opacity-90 font-bold text-xs rounded-xl shadow-xs transition">
                                    Book This Vehicle
                                </a>
                            </div>

                            <!-- Reserved Ranges with Booking Status Badge for this car -->
                            <div class="pt-2">
                                <span class="text-[11px] font-bold opacity-75 uppercase tracking-wider block mb-1 {{ $pal['text'] }}">Booked Days in {{ $currentDate->format('M Y') }}:</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($vBookings as $vb)
                                        <a href="{{ route('bookings.show', $vb->id) }}" class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl text-xs font-bold border transition shadow-xs {{ $pal['pill'] }} hover:scale-105">
                                            <span>📅 {{ \Carbon\Carbon::parse($vb->start_date)->format('M d') }} to {{ \Carbon\Carbon::parse($vb->end_date)->format('M d') }}</span>
                                            <!-- Booking Status Badge -->
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider text-white {{ $vb->status === 'confirmed' ? 'bg-emerald-600' : ($vb->status === 'completed' ? 'bg-blue-600' : ($vb->status === 'pending' ? 'bg-amber-500' : 'bg-rose-600')) }}">
                                                {{ $vb->status }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] {{ $pal['badge'] }} font-mono">{{ $vb->customer_name }}</span>
                                        </a>
                                    @empty
                                        <span class="text-xs font-bold bg-white/60 dark:bg-black/20 px-3 py-1 rounded-lg {{ $pal['text'] }}">
                                            ✓ Fully Available All Days
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
