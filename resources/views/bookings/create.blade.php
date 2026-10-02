<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('Create Car Rental Booking') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Book a specific vehicle, set destination across Mindanao (Region &rarr; Province &rarr; City) with custom rental pricing.</p>
            </div>
            <a href="{{ route('calendar.index') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium text-sm rounded-xl shadow transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                View Booking Calendar
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="bookingCalculator()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200 rounded-r-xl shadow-sm">
                    <h4 class="font-bold text-sm mb-1">Booking Conflict / Validation Error:</h4>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                
                <form method="POST" action="{{ route('bookings.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- 1. Vehicle Selection -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                            <span>1. Select Specific Vehicle</span>
                            <span class="text-xs font-normal text-gray-400">Step 1 of 4</span>
                        </h3>

                        <div>
                            <x-input-label for="vehicle_id" :value="__('Available Car Fleet')" />
                            <select id="vehicle_id" name="vehicle_id" x-model="selectedVehicleId" @change="onVehicleChange()" class="mt-1 block w-full text-base font-semibold border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3">
                                <option value="">-- Choose a Car from Fleet --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" 
                                            {{ (string)old('vehicle_id', $selectedVehicleId) === (string)$vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->name }} ({{ $vehicle->license_plate }}) — ₱{{ number_format($vehicle->daily_rate, 2) }}/day [{{ $vehicle->transmission }}, {{ $vehicle->seats }} Seats]
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('vehicle_id')" />
                        </div>
                    </div>

                    <!-- Selected Vehicle Specs & Photo Summary Box -->
                    <template x-if="selectedVehicleId && activeVehicle">
                        <div class="p-4 bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4 shadow-md overflow-hidden border border-slate-700">
                            <div class="flex items-center space-x-4 w-full md:w-auto">
                                <!-- Photo thumbnail -->
                                <template x-if="activeVehicle.image">
                                    <img :src="activeVehicle.image" class="w-28 h-20 rounded-xl object-cover border border-white/20 shadow-sm flex-shrink-0" :alt="activeVehicle.name">
                                </template>
                                <template x-if="!activeVehicle.image">
                                    <div class="w-20 h-20 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0 text-slate-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </template>

                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider bg-emerald-500 text-white px-2 py-0.5 rounded">Selected Car</span>
                                    <h4 class="text-lg font-extrabold text-white leading-tight" x-text="activeVehicle.name"></h4>
                                    <p class="text-xs text-indigo-200">
                                        Plate: <span class="font-mono font-bold" x-text="activeVehicle.plate"></span> | 
                                        Transmission: <span x-text="activeVehicle.trans"></span> | 
                                        Seats: <span x-text="activeVehicle.seats"></span> Passengers
                                    </p>
                                </div>
                            </div>

                            <div class="text-right w-full md:w-auto border-t md:border-t-0 pt-2 md:pt-0 border-slate-800">
                                <span class="text-xs text-indigo-200 uppercase font-bold tracking-wider">Starting Daily Price</span>
                                <div class="text-2xl font-black text-emerald-400">
                                    ₱<span x-text="formatMoney(activeVehicle.rate)"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 2. Wired Mindanao Destination Selection (Region -> Province -> City/Municipality) -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                            <span>2. Select Mindanao Destination (Region &rarr; Province &rarr; City/Municipality)</span>
                            <span class="text-xs font-normal text-gray-400">Step 2 of 4</span>
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- 1. Region Dropdown -->
                            <div>
                                <x-input-label for="region_select" :value="__('1. Select Region')" />
                                <select id="region_select" x-model="selectedRegion" @change="onRegionChange()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3 font-semibold text-xs">
                                    <option value="">-- Choose Region --</option>
                                    <template x-for="regionName in availableRegions" :key="regionName">
                                        <option :value="regionName" x-text="regionName"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- 2. Province Dropdown -->
                            <div>
                                <x-input-label for="province_select" :value="__('2. Select Province')" />
                                <select id="province_select" x-model="selectedProvince" @change="onProvinceChange()" :disabled="!selectedRegion" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3 font-semibold text-xs disabled:opacity-50">
                                    <option value="">-- Choose Province --</option>
                                    <template x-for="provName in availableProvinces" :key="provName">
                                        <option :value="provName" x-text="provName"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- 3. City / Municipality Dropdown -->
                            <div>
                                <x-input-label for="destination_id" :value="__('3. Select City & Rental Fee')" />
                                <select id="destination_id" name="destination_id" x-model="selectedDestinationId" @change="onDestinationChange()" :disabled="!selectedProvince" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-3 font-semibold text-xs disabled:opacity-50" required>
                                    <option value="">-- Choose City / Municipality --</option>
                                    <template x-for="item in availableCities" :key="item.id">
                                        <option :value="item.id" 
                                                x-text="`${item.city} — ${getCityRate(item) > 0 ? '+₱' + formatMoney(getCityRate(item)) + ' rental fee' : '₱0.00 (Base Area)'}`">
                                        </option>
                                    </template>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('destination_id')" />
                            </div>
                        </div>

                        <!-- Active Destination Summary Card -->
                        <template x-if="selectedDestinationId && activeDestination">
                            <div class="mt-4 p-4 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-2xl flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2.5 bg-sky-500 text-white rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-sky-700 dark:text-sky-300 font-extrabold uppercase tracking-wider">Confirmed Mindanao Route</div>
                                        <div class="text-base font-extrabold text-gray-900 dark:text-white" x-text="`${activeDestination.region} — ${activeDestination.province} — ${activeDestination.city}`"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="activeDestination.description || 'Standard destination route in Mindanao'"></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Destination Fee</div>
                                    <div class="text-lg font-black text-sky-600 dark:text-sky-400">
                                        ₱<span x-text="formatMoney(activeDestination.rate)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- 3. Select Specific Days with Booked Date Hover Popovers & Disabled States -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                            <span>3. Book for Specific Days</span>
                            <span class="text-xs font-normal text-gray-400">Step 3 of 4</span>
                        </h3>

                        <!-- Interactive Availability Month Picker -->
                        <template x-if="selectedVehicleId">
                            <div class="p-6 bg-gray-50 dark:bg-gray-900/60 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-4 mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>📅 Calendar
                                            <span class="text-xs font-normal text-gray-500">(Hover red dates to see customer details & destination)</span>
                                        </h4>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" @click="prevCalendarMonth()" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border rounded-lg text-xs font-bold hover:bg-gray-100">&larr; Prev</button>
                                        <span class="text-xs font-extrabold text-gray-800 dark:text-gray-200 min-w-[120px] text-center" x-text="calendarMonthLabel"></span>
                                        <button type="button" @click="nextCalendarMonth()" class="px-2.5 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border rounded-lg text-xs font-bold hover:bg-gray-100">Next &rarr;</button>
                                    </div>
                                </div>

                                <!-- Grid of Days -->
                                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                    <div class="font-bold text-rose-500 py-1">Sun</div>
                                    <div class="font-bold text-gray-500 py-1">Mon</div>
                                    <div class="font-bold text-gray-500 py-1">Tue</div>
                                    <div class="font-bold text-gray-500 py-1">Wed</div>
                                    <div class="font-bold text-gray-500 py-1">Thu</div>
                                    <div class="font-bold text-gray-500 py-1">Fri</div>
                                    <div class="font-bold text-gray-500 py-1">Sat</div>

                                    <template x-for="day in calendarDays" :key="day.dateStr">
                                        <div class="relative group" x-data="{ showPopover: false }">
                                            <!-- Full Booked Date Cell (Disabled & Hover Popover) -->
                                            <template x-if="day.isBooked">
                                                <div @mouseenter="showPopover = true" 
                                                     @mouseleave="showPopover = false" 
                                                     class="h-14 p-1 rounded-xl bg-rose-100 dark:bg-rose-950/80 border border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 cursor-not-allowed flex flex-col justify-between items-center opacity-90 transition">
                                                    <span class="text-xs font-black" x-text="day.dayNum"></span>
                                                    <span class="text-[9px] font-black uppercase tracking-wider bg-rose-600 text-white px-1 py-0.2 rounded shadow-xs">Booked</span>
                                                    
                                                    <!-- Hover Popover -->
                                                    <div x-show="showPopover" 
                                                         x-transition
                                                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white rounded-xl shadow-2xl z-50 text-left pointer-events-none border border-slate-700 space-y-1">
                                                        <div class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">
                                                            🔒 Fully Reserved Vehicle Date
                                                        </div>
                                                        <p class="text-xs font-bold text-white">
                                                            Customer: <span class="text-emerald-400 font-extrabold" x-text="day.bookingInfo.customer_name"></span>
                                                        </p>
                                                        <p class="text-xs text-slate-300">
                                                            Destination: <span class="italic text-sky-300 font-medium" x-text="day.bookingInfo.destination || 'Standard Car Rental'"></span>
                                                        </p>
                                                        <p class="text-[10px] text-slate-400 border-t border-slate-800 pt-1 mt-1">
                                                            Dates: <span x-text="day.bookingInfo.start_date"></span> to <span x-text="day.bookingInfo.end_date"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Partial Booking Date Cell (Selectable with info) -->
                                            <template x-if="!day.isBooked && day.isPartial && day.isCurrentMonth">
                                                <button type="button" 
                                                        @mouseenter="showPopover = true"
                                                        @mouseleave="showPopover = false"
                                                        @click="selectCalendarDate(day.dateStr)" 
                                                        :class="{
                                                            'bg-emerald-600 text-white border-emerald-700 shadow-md scale-105': isSelectedDate(day.dateStr),
                                                            'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 border-amber-300 dark:border-amber-700 hover:bg-amber-100': !isSelectedDate(day.dateStr)
                                                        }"
                                                        class="w-full h-14 p-1 rounded-xl border flex flex-col justify-between items-center transition font-semibold">
                                                    <span class="text-xs font-black" x-text="day.dayNum"></span>
                                                    <span class="text-[9px] font-bold px-1 rounded bg-amber-500 text-white" x-text="isSelectedDate(day.dateStr) ? 'Selected' : 'Has Booking'"></span>

                                                    <!-- Hover Popover for Partial Days -->
                                                    <div x-show="showPopover" 
                                                         x-transition
                                                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3 bg-slate-900 text-white rounded-xl shadow-2xl z-50 text-left pointer-events-none border border-slate-700 space-y-1">
                                                        <div class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">
                                                            ⏰ Partial Booking / Return Date
                                                        </div>
                                                        <p class="text-xs font-bold text-white">
                                                            Customer: <span class="text-emerald-400 font-extrabold" x-text="day.bookingInfo.customer_name"></span>
                                                        </p>
                                                        <p class="text-xs text-slate-300">
                                                            Reserved: <span class="font-bold text-amber-300" x-text="day.bookingInfo.pickup_time || '00:00'"></span> to <span class="font-bold text-amber-300" x-text="day.bookingInfo.return_time || '23:59'"></span>
                                                        </p>
                                                        <p class="text-[10px] text-sky-300 italic border-t border-slate-800 pt-1 mt-1">
                                                            + 2h Carwash Buffer after return time. Available for booking outside reserved window!
                                                        </p>
                                                    </div>
                                                </button>
                                            </template>

                                            <!-- Available Date Cell (Selectable) -->
                                            <template x-if="!day.isBooked && !day.isPartial && day.isCurrentMonth">
                                                <button type="button" 
                                                        @click="selectCalendarDate(day.dateStr)" 
                                                        :class="{
                                                            'bg-emerald-600 text-white border-emerald-700 shadow-md scale-105': isSelectedDate(day.dateStr),
                                                            'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 hover:border-emerald-300': !isSelectedDate(day.dateStr)
                                                        }"
                                                        class="w-full h-14 p-1 rounded-xl border flex flex-col justify-between items-center transition font-semibold">
                                                    <span class="text-xs font-black" x-text="day.dayNum"></span>
                                                    <span class="text-[9px] font-bold" x-text="isSelectedDate(day.dateStr) ? 'Selected' : 'Available'"></span>
                                                </button>
                                            </template>

                                            <!-- Empty / Other Month Cell -->
                                            <template x-if="!day.isBooked && !day.isPartial && !day.isCurrentMonth">
                                                <div class="h-14 p-1 rounded-xl bg-gray-100/50 dark:bg-gray-800/30 text-gray-400 border border-transparent flex items-center justify-center">
                                                    <span class="text-xs" x-text="day.dayNum"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pickup Date & Pickup Time Container -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Start Date Input -->
                                <div>
                                    <x-input-label for="start_date" :value="__('Pickup / Start Date *')" />
                                    <input type="date" id="start_date" name="start_date" x-model="startDate" @change="calculateTotal()" min="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5 font-semibold text-xs" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                                </div>

                                <!-- Pickup Time Input -->
                                <div>
                                    <x-input-label for="pickup_time" :value="__('Pickup Time *')" />
                                    <input type="time" id="pickup_time" name="pickup_time" x-model="pickupTime" @change="calculateTotal()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5 font-semibold text-xs" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('pickup_time')" />
                                </div>
                            </div>

                            <!-- Return Date & Return Time Container -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- End Date Input -->
                                <div>
                                    <x-input-label for="end_date" :value="__('Return / End Date *')" />
                                    <input type="date" id="end_date" name="end_date" x-model="endDate" @change="calculateTotal()" :min="startDate" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5 font-semibold text-xs" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                                </div>

                                <!-- Return Time Input -->
                                <div>
                                    <x-input-label for="return_time" :value="__('Return Time *')" />
                                    <input type="time" id="return_time" name="return_time" x-model="returnTime" @change="calculateTotal()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 p-2.5 font-semibold text-xs" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('return_time')" />
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Conflict Warning Alert Box -->
                        <template x-if="hasConflict">
                            <div class="mt-4 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 rounded-xl flex items-center space-x-3 text-rose-800 dark:text-rose-200 text-xs font-bold">
                                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    <span class="block uppercase font-black tracking-wider text-[10px] text-rose-600 dark:text-rose-400">⚠️ Schedule Conflict Detected (2-Hour Carwash Buffer)</span>
                                    <span x-text="conflictMessage"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Live Rental Summary Breakdown -->
                    <div class="p-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40 rounded-2xl">
                        <h4 class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider mb-3">Live Cost Breakdown</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            <div class="space-y-1">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Destination Fee Subtotal</p>
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200">
                                    <span x-text="totalDays"></span> Day(s) × ₱<span x-text="formatMoney(activeDestination.rate || 0)"></span>
                                </p>
                                <template x-if="excessHours > 0">
                                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400">
                                        + <span x-text="excessHours"></span> Excess Hr(s) × ₱200.00 = +₱<span x-text="formatMoney(excessHours * 200)"></span>
                                    </p>
                                </template>
                                <p class="text-xs font-bold text-emerald-600">
                                    = ₱<span x-text="formatMoney((totalDays * (activeDestination.rate || 0)) + (excessHours * 200))"></span>
                                </p>
                                <p class="text-[10px] text-gray-400 truncate" x-text="activeDestination.city ? activeDestination.region + ' — ' + activeDestination.province + ' — ' + activeDestination.city : 'No Destination Selected'"></p>
                            </div>

                            <div class="space-y-1 border-t md:border-t-0 md:border-l pt-2 md:pt-0 md:pl-4 border-emerald-200 dark:border-emerald-800">
                                <p class="text-xs text-gray-500 uppercase font-semibold">Less: Reservation Fee</p>
                                <p class="text-sm font-extrabold text-rose-600 dark:text-rose-400">
                                    - ₱<span x-text="formatMoney(reservationFee || 0)"></span>
                                </p>
                                <p class="text-[10px] text-gray-400">Deducted from Total</p>
                            </div>

                            <div class="text-right border-t md:border-t-0 md:border-l pt-2 md:pt-0 md:pl-4 border-emerald-200 dark:border-emerald-800">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Total Rental Price</span>
                                <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400">
                                    ₱<span x-text="formatMoney(totalPrice)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Customer Information & Rental Requirements -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700 mb-4 flex items-center justify-between">
                            <span>4. Customer Details & Rental Requirements</span>
                            <span class="text-xs font-normal text-gray-400">Step 4 of 4</span>
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Name -->
                            <div>
                                <x-input-label for="customer_name" :value="__('Full Name *')" />
                                <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full" :value="old('customer_name', '')" required placeholder="e.g. Juan Dela Cruz" />
                                <x-input-error class="mt-2" :messages="$errors->get('customer_name')" />
                            </div>

                            <!-- Customer Phone -->
                            <div>
                                <x-input-label for="customer_phone" :value="__('Mobile / Contact Number *')" />
                                <x-text-input id="customer_phone" name="customer_phone" type="text" class="mt-1 block w-full" :value="old('customer_phone')" required placeholder="e.g. +63 917 123 4567" />
                                <x-input-error class="mt-2" :messages="$errors->get('customer_phone')" />
                            </div>

                            <!-- Pickup Location -->
                            <div>
                                <x-input-label for="pickup_location" :value="__('Pickup Location *')" />
                                <x-text-input id="pickup_location" name="pickup_location" type="text" class="mt-1 block w-full" :value="old('pickup_location', '')" required placeholder="e.g. Bancasi Airport (BXU) / Hotel Lobby / City Center" />
                                <x-input-error class="mt-2" :messages="$errors->get('pickup_location')" />
                            </div>

                            <!-- Reservation Fee -->
                            <div>
                                <x-input-label for="reservation_fee" :value="__('Reservation Fee (₱)')" />
                                <div class="relative mt-1">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-black text-gray-400">₱</span>
                                    <x-text-input id="reservation_fee" name="reservation_fee" type="number" step="0.01" min="0" class="pl-7 block w-full text-sm font-extrabold text-emerald-600 dark:text-emerald-400" x-model="reservationFee" @input="calculateTotal()" placeholder="0.00" />
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('reservation_fee')" />
                            </div>

                            <!-- Driver's License Picture Upload -->
                            <div class="md:col-span-2">
                                <x-input-label for="driver_license" :value="__('Driver\'s License Picture of Renter')" />
                                <input id="driver_license" name="driver_license" type="file" accept="image/*" class="mt-1 block w-full text-xs text-gray-900 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl cursor-pointer p-2 focus:outline-none">
                                <x-input-error class="mt-2" :messages="$errors->get('driver_license')" />
                            </div>
                        </div>

                        <!-- Special Notes -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Special Instructions / Pickup Location Details')" />
                            <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm" placeholder="e.g. Pickup at Bancasi Airport (BXU) at 9:00 AM, child seat requested...">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('bookings.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 font-medium text-sm rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" :disabled="!selectedVehicleId || !selectedDestinationId || totalDays <= 0 || hasConflict" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-extrabold text-sm rounded-xl shadow-lg transition">
                            Confirm & Reserve Booking
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function bookingCalculator() {
            return {
                selectedVehicleId: '{{ old("vehicle_id", $selectedVehicleId ?? "") }}',
                selectedRegion: '',
                selectedProvince: '',
                selectedDestinationId: '{{ old("destination_id", "") }}',
                startDate: '{{ old("start_date", $startDate ?? date("Y-m-d")) }}',
                endDate: '{{ old("end_date", $endDate ?? date("Y-m-d", strtotime("+1 day"))) }}',
                pickupTime: '{{ old("pickup_time", "") }}',
                returnTime: '{{ old("return_time", "") }}',
                reservationFee: '{{ old("reservation_fee", 0) }}',
                totalDays: 1,
                excessHours: 0,
                totalPrice: 0,
                hasConflict: false,
                conflictMessage: '',
                activeVehicle: { name: '', plate: '', rate: 0, trans: '', seats: '', image: '', bookings: [] },
                activeDestination: { id: '', region: '', province: '', city: '', rate: 0, description: '' },
                availableRegions: [],
                availableProvinces: [],
                availableCities: [],
                
                // Calendar State
                calendarYear: new Date().getFullYear(),
                calendarMonth: new Date().getMonth(),
                calendarMonthLabel: '',
                calendarDays: [],

                destinationsData: {
                    @foreach($destinations as $d)
                        @php
                            $tRates = [];
                            foreach ($d->vehicleRates as $vr) {
                                $tRates[$vr->vehicle_type_id] = (float)$vr->destination_rate;
                            }
                        @endphp
                        '{{ $d->id }}': {
                            id: '{{ $d->id }}',
                            region: @json($d->region),
                            province: @json($d->province),
                            city: @json($d->city),
                            base_rate: {{ $d->destination_rate }},
                            type_rates: @json($tRates),
                            description: @json($d->description)
                        },
                    @endforeach
                },

                destinationsHierarchyData: @json($destinationsHierarchy),

                vehiclesData: {
                    @foreach($vehicles as $v)
                        '{{ $v->id }}': {
                            id: '{{ $v->id }}',
                            vehicle_type_id: '{{ $v->vehicle_type_id }}',
                            vehicle_type_name: @json($v->vehicleType?->name ?? 'Standard'),
                            name: @json($v->name),
                            plate: @json($v->license_plate),
                            rate: {{ $v->daily_rate }},
                            trans: @json($v->transmission),
                            seats: '{{ $v->seats }}',
                            image: '{{ $v->image_path ? asset($v->image_path) : "" }}',
                            bookings: [
                                @foreach($v->bookings as $bk)
                                    {
                                        id: '{{ $bk->id }}',
                                        start_date: '{{ $bk->start_date->format("Y-m-d") }}',
                                        end_date: '{{ $bk->end_date->format("Y-m-d") }}',
                                        pickup_time: @json($bk->pickup_time ?? '00:00'),
                                        return_time: @json($bk->return_time ?? '23:59'),
                                        customer_name: @json($bk->customer_name),
                                        destination: @json($bk->destination ?? ''),
                                        notes: @json($bk->notes ?? 'Standard Car Rental')
                                    },
                                @endforeach
                            ]
                        },
                    @endforeach
                },

                init() {
                    this.availableRegions = Object.keys(this.destinationsHierarchyData || {});

                    // Restore or set defaults
                    if (this.selectedDestinationId && this.destinationsData[this.selectedDestinationId]) {
                        this.activeDestination = this.destinationsData[this.selectedDestinationId];
                        this.selectedRegion = this.activeDestination.region;
                        this.availableProvinces = Object.keys(this.destinationsHierarchyData[this.selectedRegion] || {});
                        this.selectedProvince = this.activeDestination.province;
                        this.availableCities = (this.destinationsHierarchyData[this.selectedRegion] && this.destinationsHierarchyData[this.selectedRegion][this.selectedProvince]) || [];
                    } else if (this.availableRegions.includes('Region XIII (Caraga)')) {
                        // Default to Region XIII (Caraga) -> Agusan del Norte -> Butuan City
                        this.selectedRegion = 'Region XIII (Caraga)';
                        this.availableProvinces = Object.keys(this.destinationsHierarchyData[this.selectedRegion] || {});
                        if (this.availableProvinces.includes('Agusan del Norte')) {
                            this.selectedProvince = 'Agusan del Norte';
                            this.availableCities = this.destinationsHierarchyData[this.selectedRegion][this.selectedProvince] || [];
                            const defaultCity = this.availableCities.find(c => c.city === 'Butuan City');
                            if (defaultCity) {
                                this.selectedDestinationId = defaultCity.id;
                                this.activeDestination = {
                                    id: defaultCity.id,
                                    region: this.selectedRegion,
                                    province: this.selectedProvince,
                                    city: defaultCity.city,
                                    rate: this.getCityRate(defaultCity),
                                    description: defaultCity.description
                                };
                            }
                        }
                    }

                    this.onVehicleChange();
                    this.calculateTotal();
                },

                onRegionChange() {
                    if (this.selectedRegion && this.destinationsHierarchyData[this.selectedRegion]) {
                        this.availableProvinces = Object.keys(this.destinationsHierarchyData[this.selectedRegion]);
                    } else {
                        this.availableProvinces = [];
                    }
                    this.selectedProvince = '';
                    this.availableCities = [];
                    this.selectedDestinationId = '';
                    this.activeDestination = { id: '', region: '', province: '', city: '', rate: 0, description: '' };
                    this.calculateTotal();
                },

                onProvinceChange() {
                    if (this.selectedRegion && this.selectedProvince && this.destinationsHierarchyData[this.selectedRegion] && this.destinationsHierarchyData[this.selectedRegion][this.selectedProvince]) {
                        this.availableCities = this.destinationsHierarchyData[this.selectedRegion][this.selectedProvince];
                    } else {
                        this.availableCities = [];
                    }
                    this.selectedDestinationId = '';
                    this.activeDestination = { id: '', region: '', province: '', city: '', rate: 0, description: '' };
                    this.calculateTotal();
                },

                onDestinationChange() {
                    if (this.selectedDestinationId && this.destinationsData[this.selectedDestinationId]) {
                        this.activeDestination = this.destinationsData[this.selectedDestinationId];
                    } else {
                        this.activeDestination = { id: '', region: '', province: '', city: '', rate: 0, description: '' };
                    }
                    this.calculateTotal();
                },

                onVehicleChange() {
                    if (this.selectedVehicleId && this.vehiclesData[this.selectedVehicleId]) {
                        this.activeVehicle = this.vehiclesData[this.selectedVehicleId];
                    } else {
                        this.activeVehicle = { name: '', plate: '', rate: 0, trans: '', seats: '', image: '', bookings: [] };
                    }
                    this.renderCalendar();
                    this.calculateTotal();
                },

                renderCalendar() {
                    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    this.calendarMonthLabel = monthNames[this.calendarMonth] + ' ' + this.calendarYear;

                    const firstDay = new Date(this.calendarYear, this.calendarMonth, 1);
                    const lastDay = new Date(this.calendarYear, this.calendarMonth + 1, 0);
                    const startingDay = firstDay.getDay();
                    const totalDaysInMonth = lastDay.getDate();

                    const days = [];

                    // Previous month trailing days
                    const prevMonthLastDay = new Date(this.calendarYear, this.calendarMonth, 0).getDate();
                    for (let i = startingDay - 1; i >= 0; i--) {
                        const d = new Date(this.calendarYear, this.calendarMonth - 1, prevMonthLastDay - i);
                        const dateStr = this.formatDateStr(d);
                        days.push(this.buildDayObject(d, dateStr, false));
                    }

                    // Current month days
                    for (let i = 1; i <= totalDaysInMonth; i++) {
                        const d = new Date(this.calendarYear, this.calendarMonth, i);
                        const dateStr = this.formatDateStr(d);
                        days.push(this.buildDayObject(d, dateStr, true));
                    }

                    this.calendarDays = days;
                },

                buildDayObject(dObj, dateStr, isCurrentMonth) {
                    let isBooked = false;
                    let isPartial = false;
                    let bookingInfo = null;

                    if (this.activeVehicle && this.activeVehicle.bookings) {
                        for (let bk of this.activeVehicle.bookings) {
                            if (dateStr > bk.start_date && dateStr < bk.end_date) {
                                isBooked = true;
                                bookingInfo = bk;
                                break;
                            } else if (dateStr === bk.start_date || dateStr === bk.end_date) {
                                isPartial = true;
                                bookingInfo = bk;
                            }
                        }
                    }

                    return {
                        dateStr: dateStr,
                        dayNum: dObj.getDate(),
                        isCurrentMonth: isCurrentMonth,
                        isBooked: isBooked,
                        isPartial: isPartial,
                        bookingInfo: bookingInfo
                    };
                },

                prevCalendarMonth() {
                    if (this.calendarMonth === 0) {
                        this.calendarMonth = 11;
                        this.calendarYear--;
                    } else {
                        this.calendarMonth--;
                    }
                    this.renderCalendar();
                },

                nextCalendarMonth() {
                    if (this.calendarMonth === 11) {
                        this.calendarMonth = 0;
                        this.calendarYear++;
                    } else {
                        this.calendarMonth++;
                    }
                    this.renderCalendar();
                },

                selectCalendarDate(dateStr) {
                    if (!this.startDate || (this.startDate && this.endDate && this.startDate !== this.endDate)) {
                        this.startDate = dateStr;
                        this.endDate = dateStr;
                    } else if (dateStr >= this.startDate) {
                        this.endDate = dateStr;
                    } else {
                        this.startDate = dateStr;
                        this.endDate = dateStr;
                    }
                    this.calculateTotal();
                },

                isSelectedDate(dateStr) {
                    if (!this.startDate) return false;
                    if (!this.endDate) return dateStr === this.startDate;
                    return dateStr >= this.startDate && dateStr <= this.endDate;
                },

                formatDateStr(d) {
                    const y = d.getFullYear();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${y}-${m}-${day}`;
                },

                getCityRate(item) {
                    if (!item) return 0;
                    const vTypeId = this.activeVehicle ? this.activeVehicle.vehicle_type_id : null;
                    if (vTypeId && item.type_rates && item.type_rates[vTypeId] !== undefined) {
                        return parseFloat(item.type_rates[vTypeId]);
                    }
                    return parseFloat(item.base_rate || item.rate || 0);
                },

                getDestinationRate() {
                    if (!this.activeDestination || !this.selectedDestinationId) return 0;
                    const dest = this.destinationsData[this.selectedDestinationId];
                    if (!dest) return 0;

                    return this.getCityRate(dest);
                },

                checkScheduleConflict() {
                    this.hasConflict = false;
                    this.conflictMessage = '';

                    if (!this.selectedVehicleId || !this.vehiclesData[this.selectedVehicleId]) return;
                    if (!this.startDate || !this.endDate) return;

                    const pTime = this.pickupTime || '00:00';
                    const rTime = this.returnTime || '23:59';

                    const reqStart = new Date(this.startDate + 'T' + pTime);
                    const reqEnd = new Date(this.endDate + 'T' + rTime);

                    if (isNaN(reqStart.getTime()) || isNaN(reqEnd.getTime())) return;

                    const reqEndWithBuffer = new Date(reqEnd.getTime() + (2 * 60 * 60 * 1000));
                    const vehicle = this.vehiclesData[this.selectedVehicleId];

                    if (!vehicle || !vehicle.bookings) return;

                    for (let bk of vehicle.bookings) {
                        const bkPTime = bk.pickup_time || '00:00';
                        const bkRTime = bk.return_time || '23:59';

                        const bStart = new Date(bk.start_date + 'T' + bkPTime);
                        const bEnd = new Date(bk.end_date + 'T' + bkRTime);

                        if (isNaN(bStart.getTime()) || isNaN(bEnd.getTime())) continue;

                        const bEndWithBuffer = new Date(bEnd.getTime() + (2 * 60 * 60 * 1000));

                        // Conflict check: reqStart < bEndWithBuffer AND reqEndWithBuffer > bStart
                        if (reqStart < bEndWithBuffer && reqEndWithBuffer > bStart) {
                            this.hasConflict = true;
                            const availHours = bEndWithBuffer.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            this.conflictMessage = `Vehicle is reserved by ${bk.customer_name} until ${bk.end_date} ${bkRTime} (+2h Carwash Buffer). Available from ${availHours} onwards.`;
                            break;
                        }
                    }
                },

                calculateTotal() {
                    this.checkScheduleConflict();

                    if (!this.startDate || !this.endDate) {
                        this.totalDays = 0;
                        this.excessHours = 0;
                        this.totalPrice = 0;
                        return;
                    }
                    const pTime = this.pickupTime || '00:00';
                    const rTime = this.returnTime || '00:00';

                    const start = new Date(this.startDate + 'T' + pTime);
                    const end = new Date(this.endDate + 'T' + rTime);

                    const diffMs = end.getTime() - start.getTime();
                    if (isNaN(diffMs) || diffMs <= 0) {
                        this.totalDays = 1;
                        this.excessHours = 0;
                    } else {
                        const totalMinutes = Math.max(0, Math.ceil(diffMs / (1000 * 60)));
                        const totalHours = Math.ceil(totalMinutes / 60);

                        if (totalHours <= 24) {
                            this.totalDays = 1;
                            this.excessHours = 0;
                        } else {
                            const fullDays = Math.floor(totalHours / 24);
                            const remHours = totalHours % 24;
                            if (remHours > 5) {
                                this.totalDays = fullDays + 1;
                                this.excessHours = 0;
                            } else {
                                this.totalDays = fullDays;
                                this.excessHours = remHours;
                            }
                        }
                    }

                    const destinationRate = this.getDestinationRate();
                    this.activeDestination.rate = destinationRate;
                    const excessFee = this.excessHours * 200;
                    const destinationSubtotal = (this.totalDays * destinationRate) + excessFee;
                    const resFee = parseFloat(this.reservationFee || 0);
                    this.totalPrice = Math.max(0, destinationSubtotal - resFee);
                },

                formatMoney(amount) {
                    return Number(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</x-app-layout>
