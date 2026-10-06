<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    {{ __('All Listing') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Browse, filter, and inspect all car listings across Mindanao hosts and fleets.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold text-sm rounded-xl shadow-xs transition duration-150 border border-gray-200 dark:border-gray-700">
                    <svg class="w-4 h-4 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    View My Fleet ({{ Auth::user()->name }})
                </a>
                <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-md transition duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Register Vehicle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        modalOpen: false,
        vehicleName: '',
        licensePlate: '',
        images: [],
        currentIndex: 0,
        mobileFilterOpen: false,
        openGallery(name, plate, imageList) {
            this.vehicleName = name;
            this.licensePlate = plate;
            this.images = (imageList && imageList.length > 0) ? imageList.map(img => (img.startsWith('http') || img.startsWith('/') ? img : '/' + img)) : ['/images/placeholder.jpg'];
            this.currentIndex = 0;
            this.modalOpen = true;
            document.body.classList.add('overflow-hidden');
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.modalOpen = false;
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
        },
        next() {
            if (this.images.length > 0) {
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
            }
        },
        prev() {
            if (this.images.length > 0) {
                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            }
        }
    }" @keydown.window.escape="closeModal()" @keydown.window.arrow-right="if (modalOpen) next()" @keydown.window.arrow-left="if (modalOpen) prev()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">


            <!-- Comprehensive Vehicle Information Filter Form -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <h3 class="font-extrabold text-sm sm:text-base text-gray-900 dark:text-white">Filter Vehicle Fleet</h3>
                        @if(request()->hasAny(['search', 'vehicle_type_id', 'status', 'transmission', 'fuel_type', 'seats', 'min_price', 'max_price', 'sort']))
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Active Filters</span>
                        @endif
                    </div>
                    <button type="button" @click="mobileFilterOpen = !mobileFilterOpen" class="sm:hidden text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                        <span x-text="mobileFilterOpen ? 'Hide Filters' : 'Show All Filters'"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('vehicles.all') }}" class="space-y-4" :class="mobileFilterOpen ? 'block' : 'hidden sm:block'">
                    <!-- Row 1: Search, Vehicle Type, Status, Sort -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Search Keyword -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Search Keyword</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Make, model, color, host..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 pl-9">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        <!-- Vehicle Type -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Vehicle Type</label>
                            <select name="vehicle_type_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Vehicle Types</option>
                                @foreach($vehicleTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('vehicle_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Availability Status</label>
                            <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Statuses</option>
                                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>🟢 Available</option>
                                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>🟡 In Maintenance</option>
                                <option value="out_of_service" {{ request('status') === 'out_of_service' ? 'selected' : '' }}>🔴 Out of Service</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Sort By</label>
                            <select name="sort" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Registered</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Vehicle Name (A-Z)</option>
                                <option value="seats_desc" {{ request('sort') === 'seats_desc' ? 'selected' : '' }}>Seats: Most to Least</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Transmission, Fuel Type, Min Seats, Price Min/Max -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                        <!-- Transmission -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Transmission</label>
                            <select name="transmission" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Transmissions</option>
                                <option value="Automatic" {{ request('transmission') === 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                <option value="Manual" {{ request('transmission') === 'Manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                        </div>

                        <!-- Fuel Type -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Fuel Type</label>
                            <select name="fuel_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Fuel Types</option>
                                <option value="Gasoline" {{ request('fuel_type') === 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                <option value="Diesel" {{ request('fuel_type') === 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="Hybrid" {{ request('fuel_type') === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                <option value="Electric" {{ request('fuel_type') === 'Electric' ? 'selected' : '' }}>Electric</option>
                            </select>
                        </div>

                        <!-- Min Seats -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Minimum Seats</label>
                            <select name="seats" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Any Capacity</option>
                                <option value="4" {{ request('seats') === '4' ? 'selected' : '' }}>4+ Seats</option>
                                <option value="5" {{ request('seats') === '5' ? 'selected' : '' }}>5+ Seats</option>
                                <option value="7" {{ request('seats') === '7' ? 'selected' : '' }}>7+ Seats</option>
                                <option value="10" {{ request('seats') === '10' ? 'selected' : '' }}>10+ Seats (Vans)</option>
                                <option value="14" {{ request('seats') === '14' ? 'selected' : '' }}>14+ Seats (Commuter)</option>
                            </select>
                        </div>

                        <!-- Daily Rate Range -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Daily Rate (₱)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2">
                        @if(request()->hasAny(['search', 'vehicle_type_id', 'status', 'transmission', 'fuel_type', 'seats', 'min_price', 'max_price', 'sort']))
                            <a href="{{ route('vehicles.all') }}" class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-bold rounded-xl transition">
                                Clear Filters
                            </a>
                        @endif
                        <button type="submit" class="py-2.5 px-6 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Vehicle Listings Grid -->
            @if($vehicles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($vehicles as $vehicle)
                        <div class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden">
                            <!-- Image Cover -->
                            <div class="relative aspect-[16/10] bg-gray-100 dark:bg-gray-900 overflow-hidden cursor-pointer"
                                 @click="openGallery('{{ addslashes($vehicle->name) }}', '{{ addslashes($vehicle->license_plate) }}', {{ json_encode($vehicle->images ?? ($vehicle->image_path ? [$vehicle->image_path] : [])) }})">
                                @if($vehicle->image_path)
                                    <img src="{{ str_starts_with($vehicle->image_path, 'http') ? $vehicle->image_path : asset($vehicle->image_path) }}"
                                         alt="{{ $vehicle->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-xs font-semibold">No Image Uploaded</span>
                                    </div>
                                @endif

                                <!-- Status Badge Overlay -->
                                <div class="absolute top-3 left-3 flex items-center gap-1.5">
                                    @if($vehicle->is_rented_today)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-blue-600 text-white shadow-md shadow-blue-600/30 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            Rented
                                        </span>
                                    @elseif($vehicle->status === 'available')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-500 text-white shadow-md shadow-emerald-500/30 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            Available
                                        </span>
                                    @elseif($vehicle->status === 'maintenance')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-amber-500 text-white shadow-md shadow-amber-500/30">
                                            In Maintenance
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-rose-500 text-white shadow-md shadow-rose-500/30">
                                            Out of Service
                                        </span>
                                    @endif

                                    @if($vehicle->vehicleType)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-900/80 text-white backdrop-blur-xs">
                                            {{ $vehicle->vehicleType->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Gallery Indicator -->
                                @php
                                    $imgCount = is_array($vehicle->images) ? count($vehicle->images) : ($vehicle->image_path ? 1 : 0);
                                @endphp
                                @if($imgCount > 1)
                                    <div class="absolute bottom-3 right-3 px-2 py-0.5 rounded-lg bg-black/70 text-white text-[11px] font-bold flex items-center gap-1 backdrop-blur-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $imgCount }} Photos
                                    </div>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                <div>
                                    <!-- Title & Year -->
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="font-extrabold text-lg text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                                                {{ $vehicle->name }}
                                            </h4>
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }}) &bull; <span class="font-mono text-gray-700 dark:text-gray-300 font-bold">{{ $vehicle->license_plate }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Key Specs Pills -->
                                    <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                                        <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
                                            <span class="text-[10px] uppercase font-bold text-gray-400 block">Gearbox</span>
                                            <span class="text-xs font-black text-gray-800 dark:text-gray-200 mt-0.5 block truncate">{{ $vehicle->transmission }}</span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
                                            <span class="text-[10px] uppercase font-bold text-gray-400 block">Fuel</span>
                                            <span class="text-xs font-black text-gray-800 dark:text-gray-200 mt-0.5 block truncate">{{ $vehicle->fuel_type }}</span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
                                            <span class="text-[10px] uppercase font-bold text-gray-400 block">Capacity</span>
                                            <span class="text-xs font-black text-gray-800 dark:text-gray-200 mt-0.5 block truncate">{{ $vehicle->seats }} Seats</span>
                                        </div>
                                    </div>

                                    <!-- Host / Owner & Color -->
                                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/80 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center gap-2">
                                            @if($vehicle->user && $vehicle->user->avatar_url)
                                                <img src="{{ $vehicle->user->avatar_url }}" class="w-5 h-5 rounded-full object-cover">
                                            @else
                                                <div class="w-5 h-5 rounded-full bg-emerald-500 text-white font-bold text-[9px] flex items-center justify-center">
                                                    {{ strtoupper(substr($vehicle->user->formatted_name ?? 'O', 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="font-medium text-gray-700 dark:text-gray-300 truncate max-w-[130px]">
                                                {{ $vehicle->user->formatted_name ?? 'Verified Host' }}
                                            </span>
                                        </div>
                                        @if($vehicle->color)
                                            <span class="text-[11px] font-medium text-gray-400">Color: <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $vehicle->color }}</span></span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Bottom Price & Actions -->
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700/80 flex items-center justify-between gap-2">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Daily Rate</span>
                                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                                            ₱{{ number_format($vehicle->daily_rate, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('vehicles.show', $vehicle) }}"
                                           class="p-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold text-xs transition"
                                           title="View Specs & Schedule">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('bookings.create', ['vehicle_id' => $vehicle->id]) }}"
                                           class="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs shadow-md transition flex items-center gap-1">
                                            Book Now &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="p-12 text-center bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-lg text-gray-900 dark:text-white">No Vehicles Match Your Filters</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto mt-1">Try broadening your search keywords or resetting specific filters to see more listings.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('vehicles.all') }}" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                            Reset All Filters
                        </a>
                    </div>
                </div>
            @endif

        </div>

        <!-- Lightbox Gallery Modal -->
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
             style="display: none;">

            <div class="relative max-w-4xl w-full bg-gray-900 rounded-3xl overflow-hidden shadow-2xl border border-gray-800"
                 @click.away="closeModal()">

                <!-- Modal Header -->
                <div class="p-4 border-b border-gray-800 flex items-center justify-between text-white">
                    <div>
                        <h3 class="font-bold text-lg" x-text="vehicleName"></h3>
                        <p class="text-xs text-gray-400" x-text="licensePlate"></p>
                    </div>
                    <button @click="closeModal()" class="p-2 rounded-full hover:bg-gray-800 text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Image Slider -->
                <div class="relative aspect-video bg-black flex items-center justify-center">
                    <template x-if="images.length > 0">
                        <img :src="images[currentIndex]" class="max-h-full max-w-full object-contain">
                    </template>

                    <!-- Nav Buttons -->
                    <button x-show="images.length > 1" @click="prev()" class="absolute left-4 p-3 rounded-full bg-black/50 hover:bg-black/80 text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button x-show="images.length > 1" @click="next()" class="absolute right-4 p-3 rounded-full bg-black/50 hover:bg-black/80 text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <!-- Counter -->
                    <div x-show="images.length > 1" class="absolute bottom-4 px-3 py-1 bg-black/60 rounded-full text-xs font-bold text-white tracking-widest">
                        <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                    </div>
                </div>

                <!-- Thumbnails Strip -->
                <div x-show="images.length > 1" class="p-3 bg-gray-950 flex gap-2 overflow-x-auto">
                    <template x-for="(img, idx) in images" :key="idx">
                        <img :src="img"
                             @click="currentIndex = idx"
                             class="h-14 w-20 object-cover rounded-lg cursor-pointer border-2 transition"
                             :class="currentIndex === idx ? 'border-emerald-500 scale-105' : 'border-transparent opacity-60 hover:opacity-100'">
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
