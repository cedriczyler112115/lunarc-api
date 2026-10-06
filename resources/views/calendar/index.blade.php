<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('My Calendar') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Integrated vehicle schedule & reservation calendar with status tracking and unique vehicle colors.</p>
            </div>
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

    @php
        $defaultMobileDate = (date('Y-m') === $selectedMonth) ? date('Y-m-d') : $currentDate->copy()->startOfMonth()->format('Y-m-d');

        // Build persistent slot assignments for all bookings in this month view
        $sortedBookings = $bookings->sortBy(function($b) {
            return $b->start_date->format('Y-m-d') . '_' . sprintf('%08d', 99999999 - $b->end_date->diffInDays($b->start_date)) . '_' . sprintf('%08d', $b->id);
        });

        $bookingSlots = [];
        $trackEndDates = [];

        foreach ($sortedBookings as $bk) {
            $assignedSlot = null;
            $bkStart = $bk->start_date->format('Y-m-d');
            $bkEnd = $bk->end_date->format('Y-m-d');

            foreach ($trackEndDates as $slotIdx => $lastEndDate) {
                if ($lastEndDate < $bkStart) {
                    $assignedSlot = $slotIdx;
                    $trackEndDates[$slotIdx] = $bkEnd;
                    break;
                }
            }

            if ($assignedSlot === null) {
                $assignedSlot = count($trackEndDates);
                $trackEndDates[$assignedSlot] = $bkEnd;
            }

            $bookingSlots[$bk->id] = $assignedSlot;
        }

        $totalTracksCount = count($trackEndDates);
        $maxSlotsToDisplay = max(2, min(4, $totalTracksCount));
    @endphp

    <div class="py-8" x-data="{ activeTab: 'grid', selectedMobileDate: '{{ $defaultMobileDate }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Month Navigation & Vehicle Filter Header -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Month Navigator -->
                <div class="flex items-center justify-between md:justify-start w-full md:w-auto gap-2 sm:gap-3">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('calendar.index', ['month' => $prevMonth, 'vehicle_id' => $selectedVehicleId, 'user_id' => $selectedUserId]) }}" class="p-2 sm:p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <h3 class="text-base sm:text-xl font-extrabold text-gray-900 dark:text-white min-w-[130px] sm:min-w-[180px] text-center">
                            {{ $currentDate->format('F Y') }}
                        </h3>
                        <a href="{{ route('calendar.index', ['month' => $nextMonth, 'vehicle_id' => $selectedVehicleId, 'user_id' => $selectedUserId]) }}" class="p-2 sm:p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <a href="{{ route('calendar.index', ['month' => date('Y-m'), 'vehicle_id' => $selectedVehicleId, 'user_id' => $selectedUserId]) }}" class="px-3 py-2 text-xs font-bold bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-xl shadow-2xs hover:bg-indigo-100 transition">
                        Today
                    </a>
                </div>

                <!-- User & Vehicle Filters & View Switcher -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                    <form method="GET" action="{{ route('calendar.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full md:w-auto">
                        <input type="hidden" name="month" value="{{ $selectedMonth }}">
                        
                        <!-- User Filter Dropdown -->
                        <select name="user_id" onchange="this.form.submit()" class="w-full text-xs font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-amber-500 p-2.5">
                            <option value="all" {{ $selectedUserId === 'all' ? 'selected' : '' }}>All Users</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}" {{ (string)$selectedUserId === (string)$u->id ? 'selected' : '' }}>
                                    👤 {{ $u->name }} {{ auth()->id() === $u->id ? '(You)' : '' }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Vehicle Filter Dropdown -->
                        <select name="vehicle_id" onchange="this.form.submit()" class="w-full text-xs font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-amber-500 p-2.5">
                            <option value="">All Vehicles Fleet</option>
                            @foreach($allVehicles as $v)
                                <option value="{{ $v->id }}" {{ (string)$selectedVehicleId === (string)$v->id ? 'selected' : '' }}>
                                    {{ $v->name }} ({{ $v->license_plate }})
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <!-- View Switch Buttons -->
                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 p-1 rounded-xl w-full sm:w-auto">
                        <button @click="activeTab = 'grid'" :class="activeTab === 'grid' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'" class="flex-1 sm:flex-initial px-3 py-2 text-xs font-bold rounded-lg transition text-center">
                            Month Calendar
                        </button>
                        <button @click="activeTab = 'timeline'" :class="activeTab === 'timeline' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'" class="flex-1 sm:flex-initial px-3 py-2 text-xs font-bold rounded-lg transition text-center">
                            Vehicle Timelines
                        </button>
                    </div>
                </div>

            </div>



            <!-- TAB 1: Monthly Calendar Grid -->
            <div x-show="activeTab === 'grid'" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-visible">
                
                <!-- DESKTOP CALENDAR VIEW (Visible on >= md screens) -->
                <div class="hidden md:block">
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

                            <div class="min-h-[140px] h-auto p-2 transition hover:bg-gray-50/70 dark:hover:bg-gray-700/30 group relative hover:z-30 flex flex-col justify-start space-y-1.5 {{ !$isCurrentMonth ? 'bg-gray-50/40 dark:bg-gray-900/40 text-gray-400' : '' }}">
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
                                <div class="space-y-1.5 flex-1">
                                    @foreach($dayBookings as $bk)
                                        @php
                                            $pal = $vehicleColorMap[$bk->vehicle_id] ?? $colorPalettes[0];
                                        @endphp
                                        <div class="w-full transition-all" x-data="{ showPopover: false }" :class="showPopover ? 'z-50 relative' : 'relative z-1'">
                                            <a href="{{ route('bookings.show', $bk->id) }}" 
                                               @mouseenter="showPopover = true" 
                                               @mouseleave="showPopover = false"
                                               class="block p-1.5 rounded-xl text-[10px] leading-tight font-bold transition shadow-xs border {{ $pal['bg'] }} {{ $pal['border'] }} {{ $pal['text'] }} hover:scale-[1.02]">
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

                                            <!-- Rich Hover Popover Card -->
                                            <div x-show="showPopover" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                                 class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-md text-white rounded-2xl shadow-2xl z-[100] text-left pointer-events-none border border-slate-700/80 space-y-2.5"
                                                 style="display: none;">
                                                
                                                <!-- Header: Vehicle & Status -->
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                                    <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider truncate">
                                                        🚗 {{ $bk->vehicle->name ?? 'Vehicle' }} ({{ $bk->vehicle->license_plate ?? 'N/A' }})
                                                    </span>
                                                    <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded-full text-white {{ $bk->status === 'confirmed' ? 'bg-emerald-500' : ($bk->status === 'completed' ? 'bg-blue-500' : ($bk->status === 'pending' ? 'bg-amber-500' : 'bg-rose-500')) }}">
                                                        {{ $bk->status }}
                                                    </span>
                                                </div>

                                                <!-- 1. Renter Name & 2. Contact Number -->
                                                <div class="space-y-1">
                                                    <p class="text-xs font-black text-white flex items-center justify-between">
                                                        <span class="text-slate-400 font-semibold text-[11px]">1. Renter Name:</span>
                                                        <span class="text-emerald-400 font-bold">{{ $bk->customer_name }}</span>
                                                    </p>
                                                    <p class="text-xs font-black text-white flex items-center justify-between">
                                                        <span class="text-slate-400 font-semibold text-[11px]">2. Contact Number:</span>
                                                        <span class="text-sky-300 font-bold">📞 {{ $bk->customer_phone ?: 'N/A' }}</span>
                                                    </p>
                                                </div>

                                                <!-- 3. Destination (Format: Municipality, Province) -->
                                                @php
                                                    $dtDestText = 'Standard Rental';
                                                    if ($bk->destinationModel) {
                                                        $dtDestText = $bk->destinationModel->city . ', ' . $bk->destinationModel->province;
                                                    } elseif (!empty($bk->destination)) {
                                                        $cleanDest = preg_replace('/^Region\s+[^-\n\:]*?(\([^)]*\))?\s*[-:]?\s*/i', '', $bk->destination);
                                                        $dtDestText = trim($cleanDest) ?: $bk->destination;
                                                    }
                                                @endphp
                                                <div class="pt-1.5 border-t border-slate-800">
                                                    <span class="text-[10px] text-slate-400 font-semibold block">3. Destination:</span>
                                                    <span class="text-xs font-extrabold text-amber-300 block">📍 {{ $dtDestText }}</span>
                                                </div>

                                                <!-- 4. Pickup Date/Time & 5. Return Date/Time -->
                                                <div class="pt-1.5 border-t border-slate-800 grid grid-cols-2 gap-2 text-[11px]">
                                                    <div class="bg-slate-800/80 p-2 rounded-xl">
                                                        <span class="text-[9px] text-emerald-400 font-bold uppercase block">4. Pickup Date/Time</span>
                                                        <span class="font-extrabold text-white block">{{ $bk->start_date->format('M d, Y') }}</span>
                                                        <span class="font-bold text-emerald-300 text-[10px]">⏰ {{ \Carbon\Carbon::parse($bk->pickup_time ?: '00:00')->format('g:i A') }}</span>
                                                    </div>
                                                    <div class="bg-slate-800/80 p-2 rounded-xl">
                                                        <span class="text-[9px] text-rose-400 font-bold uppercase block">5. Return Date/Time</span>
                                                        <span class="font-extrabold text-white block">{{ $bk->end_date->format('M d, Y') }}</span>
                                                        <span class="font-bold text-rose-300 text-[10px]">⏰ {{ \Carbon\Carbon::parse($bk->return_time ?: '00:00')->format('g:i A') }}</span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @php
                                $iterDate->addDay();
                            @endphp
                        @endwhile
                    </div>
                </div>

                <!-- MOBILE CALENDAR VIEW (Visible on < md screens) -->
                <div class="block md:hidden">
                    <!-- Compact Day Headers -->
                    <div class="grid grid-cols-7 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-center py-2.5 text-xs font-black uppercase text-gray-500">
                        <div class="text-rose-500">S</div>
                        <div>M</div>
                        <div>T</div>
                        <div>W</div>
                        <div>T</div>
                        <div>F</div>
                        <div>S</div>
                    </div>

                    @php
                        $mIterDate = $startOfWeek->copy();
                    @endphp

                    <!-- Compact Mobile Calendar Days Grid -->
                    <div class="grid grid-cols-7 gap-0.5 p-2 bg-gray-50/50 dark:bg-gray-900/40">
                        @while($mIterDate <= $endOfWeek)
                            @php
                                $mDateStr = $mIterDate->format('Y-m-d');
                                $mIsCurrentMonth = $mIterDate->month === $currentDate->month;
                                $mIsToday = $mIterDate->isToday();
                                
                                $mDayBookings = $bookings->filter(function($b) use ($mDateStr) {
                                    return $mDateStr >= $b->start_date->format('Y-m-d') && $mDateStr <= $b->end_date->format('Y-m-d');
                                });
                            @endphp

                            <button type="button"
                                    @click="selectedMobileDate = '{{ $mDateStr }}'"
                                    :class="{
                                        'ring-2 ring-indigo-600 dark:ring-indigo-400 font-extrabold bg-indigo-50 dark:bg-indigo-950/80 scale-[1.04] shadow-sm z-20': selectedMobileDate === '{{ $mDateStr }}',
                                        'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-100 dark:border-gray-700': selectedMobileDate !== '{{ $mDateStr }}' && {{ $mIsCurrentMonth ? 'true' : 'false' }},
                                        'bg-gray-100/60 dark:bg-gray-900/60 text-gray-400 border-transparent': selectedMobileDate !== '{{ $mDateStr }}' && {{ !$mIsCurrentMonth ? 'true' : 'false' }}
                                    }"
                                    class="min-h-[64px] h-auto p-1 py-1.5 cursor-pointer rounded-xl border flex flex-col justify-between items-center transition relative overflow-hidden">
                                
                                <!-- Date Number -->
                                <span class="text-xs font-black px-1.5 py-0.2 rounded-full {{ $mIsToday ? 'bg-indigo-600 text-white' : ($mIsCurrentMonth ? 'text-gray-900 dark:text-white' : 'text-gray-400') }}">
                                    {{ $mIterDate->day }}
                                </span>

                                <!-- Continuous Aligned Line Ranges for Bookings covering this date -->
                                <div class="w-full flex flex-col gap-1 px-0 pb-1 mt-1">
                                    @for($s = 0; $s < $maxSlotsToDisplay; $s++)
                                        @php
                                            $slotBooking = $mDayBookings->first(function($b) use ($s, $bookingSlots) {
                                                return ($bookingSlots[$b->id] ?? null) === $s;
                                            });
                                        @endphp

                                        @if($slotBooking)
                                            @php
                                                $pal = $vehicleColorMap[$slotBooking->vehicle_id] ?? $colorPalettes[0];
                                                $bStart = $slotBooking->start_date->format('Y-m-d');
                                                $bEnd = $slotBooking->end_date->format('Y-m-d');
                                                
                                                $isTrueStart = ($mDateStr === $bStart);
                                                $isTrueEnd = ($mDateStr === $bEnd);
                                                $isSun = ($mIterDate->dayOfWeek === 0);
                                                $isSat = ($mIterDate->dayOfWeek === 6);

                                                $renderStart = $isTrueStart || $isSun;
                                                $renderEnd = $isTrueEnd || $isSat;
                                            @endphp

                                            @if($renderStart && $renderEnd)
                                                <div class="h-2 w-full {{ $pal['dot'] }} rounded-full shadow-2xs transition-all"
                                                     title="{{ $slotBooking->vehicle->name ?? 'Vehicle' }} ({{ $slotBooking->customer_name }})">
                                                </div>
                                            @elseif($renderStart && !$renderEnd)
                                                <div class="h-2 w-[calc(100%+0.75rem)] -mr-3 {{ $pal['dot'] }} rounded-l-full rounded-r-none shadow-2xs transition-all relative z-10"
                                                     title="{{ $slotBooking->vehicle->name ?? 'Vehicle' }} ({{ $slotBooking->customer_name }})">
                                                </div>
                                            @elseif(!$renderStart && !$renderEnd)
                                                <div class="h-2 w-[calc(100%+1.5rem)] -mx-3 {{ $pal['dot'] }} rounded-none shadow-2xs transition-all relative z-10"
                                                     title="{{ $slotBooking->vehicle->name ?? 'Vehicle' }} ({{ $slotBooking->customer_name }})">
                                                </div>
                                            @elseif(!$renderStart && $renderEnd)
                                                <div class="h-2 w-[calc(100%+0.75rem)] -ml-3 {{ $pal['dot'] }} rounded-r-full rounded-l-none shadow-2xs transition-all relative z-10"
                                                     title="{{ $slotBooking->vehicle->name ?? 'Vehicle' }} ({{ $slotBooking->customer_name }})">
                                                </div>
                                            @endif
                                        @else
                                            <!-- Empty Track Spacer to maintain perfect vertical line alignment -->
                                            <div class="h-2 w-full opacity-0 pointer-events-none"></div>
                                        @endif
                                    @endfor
                                </div>
                            </button>

                            @php
                                $mIterDate->addDay();
                            @endphp
                        @endwhile
                    </div>

                    <!-- Mobile Selected Date Booking Details Panel (Inside Card Below Calendar Grid) -->
                    <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 space-y-4">
                        @php
                            $panelIterDate = $startOfWeek->copy();
                        @endphp
                        @while($panelIterDate <= $endOfWeek)
                            @php
                                $pDateStr = $panelIterDate->format('Y-m-d');
                                $pDayBookings = $bookings->filter(function($b) use ($pDateStr) {
                                    return $pDateStr >= $b->start_date->format('Y-m-d') && $pDateStr <= $b->end_date->format('Y-m-d');
                                });
                            @endphp

                            <div x-show="selectedMobileDate === '{{ $pDateStr }}'" x-transition class="space-y-3">
                                <!-- Selected Date Header -->
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Selected Schedule Date</span>
                                        <h4 class="text-base font-extrabold text-gray-900 dark:text-white">
                                            {{ $panelIterDate->format('l, F j, Y') }}
                                        </h4>
                                    </div>
                                    <a href="{{ route('bookings.create', ['start_date' => $pDateStr, 'end_date' => $pDateStr, 'vehicle_id' => $selectedVehicleId]) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                        + Book For Date
                                    </a>
                                </div>

                                <!-- Bookings List Cards for this Date -->
                                @forelse($pDayBookings as $bk)
                                    @php
                                        $pal = $vehicleColorMap[$bk->vehicle_id] ?? $colorPalettes[0];
                                    @endphp
                                    <div class="p-4 rounded-2xl border space-y-3 transition {{ $pal['bg'] }} {{ $pal['border'] }}">
                                        <!-- Vehicle Header & Status Badge -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-3 h-3 rounded-full {{ $pal['dot'] }}"></span>
                                                <h5 class="text-sm font-extrabold {{ $pal['text'] }}">
                                                    🚗 {{ $bk->vehicle->name ?? 'Vehicle' }}
                                                </h5>
                                                <span class="text-xs font-mono font-bold opacity-75 {{ $pal['text'] }}">
                                                    ({{ $bk->vehicle->license_plate ?? 'N/A' }})
                                                </span>
                                            </div>
                                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-full text-white {{ $bk->status === 'confirmed' ? 'bg-emerald-600' : ($bk->status === 'completed' ? 'bg-blue-600' : ($bk->status === 'pending' ? 'bg-amber-500' : 'bg-rose-600')) }}">
                                                {{ $bk->status }}
                                            </span>
                                        </div>

                                        <!-- 5 Booking Details Required -->
                                        <div class="grid grid-cols-1 gap-2 text-xs pt-2 border-t border-black/5 dark:border-white/10">
                                            <!-- 1. Renter Name -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">1. Renter Name:</span>
                                                <span class="font-black text-gray-900 dark:text-white">{{ $bk->customer_name }}</span>
                                            </div>

                                            <!-- 2. Contact Number -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">2. Contact Number:</span>
                                                <span class="font-extrabold text-sky-600 dark:text-sky-400">📞 {{ $bk->customer_phone ?: 'N/A' }}</span>
                                            </div>

                                            <!-- 3. Destination (Format: Municipality, Province) -->
                                            @php
                                                $mobileDestText = 'Standard Rental';
                                                if ($bk->destinationModel) {
                                                    $mobileDestText = $bk->destinationModel->city . ', ' . $bk->destinationModel->province;
                                                } elseif (!empty($bk->destination)) {
                                                    $cleanDest = preg_replace('/^Region\s+[^-\n\:]*?(\([^)]*\))?\s*[-:]?\s*/i', '', $bk->destination);
                                                    $mobileDestText = trim($cleanDest) ?: $bk->destination;
                                                }
                                            @endphp
                                            <div class="flex justify-between items-center">
                                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">3. Destination:</span>
                                                <span class="font-extrabold text-amber-600 dark:text-amber-400">📍 {{ $mobileDestText }}</span>
                                            </div>

                                            <!-- 4. Pickup Date/Time -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">4. Pickup Schedule:</span>
                                                <span class="font-extrabold text-emerald-600 dark:text-emerald-400">
                                                    {{ $bk->start_date->format('M d, Y') }} ⏰ {{ \Carbon\Carbon::parse($bk->pickup_time ?: '00:00')->format('g:i A') }}
                                                </span>
                                            </div>

                                            <!-- 5. Return Date/Time -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">5. Return Schedule:</span>
                                                <span class="font-extrabold text-rose-600 dark:text-rose-400">
                                                    {{ $bk->end_date->format('M d, Y') }} ⏰ {{ \Carbon\Carbon::parse($bk->return_time ?: '00:00')->format('g:i A') }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Link to full details -->
                                        <div class="pt-2 border-t border-black/5 dark:border-white/10 flex justify-end">
                                            <a href="{{ route('bookings.show', $bk->id) }}" class="text-xs font-black text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                                <span>View Reservation Receipt</span>
                                                <span>&rarr;</span>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-gray-400 space-y-2 bg-gray-50 dark:bg-gray-900/40 rounded-2xl border border-gray-100 dark:border-gray-700">
                                        <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400">No events or car reservations scheduled for this date.</p>
                                    </div>
                                @endforelse
                            </div>

                            @php
                                $panelIterDate->addDay();
                            @endphp
                        @endwhile
                    </div>
                </div>
            </div>

            <!-- TAB 2: Vehicle Timeline Schedule Grid -->
            <div x-show="activeTab === 'timeline'" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-visible p-6 space-y-6">
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
                                        <div class="inline-block transition-all" x-data="{ showPopover: false }" :class="showPopover ? 'z-50 relative' : 'relative z-1'">
                                            <a href="{{ route('bookings.show', $vb->id) }}" 
                                               @mouseenter="showPopover = true" 
                                               @mouseleave="showPopover = false"
                                               class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl text-xs font-bold border transition shadow-xs {{ $pal['pill'] }} hover:scale-105">
                                                <span>📅 {{ \Carbon\Carbon::parse($vb->start_date)->format('M d') }} to {{ \Carbon\Carbon::parse($vb->end_date)->format('M d') }}</span>
                                                <!-- Booking Status Badge -->
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider text-white {{ $vb->status === 'confirmed' ? 'bg-emerald-600' : ($vb->status === 'completed' ? 'bg-blue-600' : ($vb->status === 'pending' ? 'bg-amber-500' : 'bg-rose-600')) }}">
                                                    {{ $vb->status }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded text-[10px] {{ $pal['badge'] }} font-mono">{{ $vb->customer_name }}</span>
                                            </a>

                                            <!-- Hover Popover Card for Timeline Pill -->
                                            <div x-show="showPopover" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                                 class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900/95 backdrop-blur-md text-white rounded-2xl shadow-2xl z-[100] text-left pointer-events-none border border-slate-700/80 space-y-2.5"
                                                 style="display: none;">
                                                
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                                    <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wider truncate">
                                                        🚗 {{ $vehicle->name }} ({{ $vehicle->license_plate }})
                                                    </span>
                                                    <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded-full text-white {{ $vb->status === 'confirmed' ? 'bg-emerald-500' : ($vb->status === 'completed' ? 'bg-blue-500' : ($vb->status === 'pending' ? 'bg-amber-500' : 'bg-rose-500')) }}">
                                                        {{ $vb->status }}
                                                    </span>
                                                </div>

                                                <div class="space-y-1">
                                                    <p class="text-xs font-black text-white flex items-center justify-between">
                                                        <span class="text-slate-400 font-semibold text-[11px]">1. Renter Name:</span>
                                                        <span class="text-emerald-400 font-bold">{{ $vb->customer_name }}</span>
                                                    </p>
                                                    <p class="text-xs font-black text-white flex items-center justify-between">
                                                        <span class="text-slate-400 font-semibold text-[11px]">2. Contact Number:</span>
                                                        <span class="text-sky-300 font-bold">📞 {{ $vb->customer_phone ?: 'N/A' }}</span>
                                                    </p>
                                                </div>

                                                <!-- 3. Destination (Format: Municipality, Province) -->
                                                @php
                                                    $tlDestText = 'Standard Rental';
                                                    if ($vb->destinationModel) {
                                                        $tlDestText = $vb->destinationModel->city . ', ' . $vb->destinationModel->province;
                                                    } elseif (!empty($vb->destination)) {
                                                        $cleanDest = preg_replace('/^Region\s+[^-\n\:]*?(\([^)]*\))?\s*[-:]?\s*/i', '', $vb->destination);
                                                        $tlDestText = trim($cleanDest) ?: $vb->destination;
                                                    }
                                                @endphp
                                                <div class="pt-1.5 border-t border-slate-800">
                                                    <span class="text-[10px] text-slate-400 font-semibold block">3. Destination:</span>
                                                    <span class="text-xs font-extrabold text-amber-300 block">📍 {{ $tlDestText }}</span>
                                                </div>

                                                <div class="pt-1.5 border-t border-slate-800 grid grid-cols-2 gap-2 text-[11px]">
                                                    <div class="bg-slate-800/80 p-2 rounded-xl">
                                                        <span class="text-[9px] text-emerald-400 font-bold uppercase block">4. Pickup Date/Time</span>
                                                        <span class="font-extrabold text-white block">{{ \Carbon\Carbon::parse($vb->start_date)->format('M d, Y') }}</span>
                                                        <span class="font-bold text-emerald-300 text-[10px]">⏰ {{ \Carbon\Carbon::parse($vb->pickup_time ?: '00:00')->format('g:i A') }}</span>
                                                    </div>
                                                    <div class="bg-slate-800/80 p-2 rounded-xl">
                                                        <span class="text-[9px] text-rose-400 font-bold uppercase block">5. Return Date/Time</span>
                                                        <span class="font-extrabold text-white block">{{ \Carbon\Carbon::parse($vb->end_date)->format('M d, Y') }}</span>
                                                        <span class="font-bold text-rose-300 text-[10px]">⏰ {{ \Carbon\Carbon::parse($vb->return_time ?: '00:00')->format('g:i A') }}</span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
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
