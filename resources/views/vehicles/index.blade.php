<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    {{ __('Vehicle Data Entry & Management') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage rental car entries, photos, specifications, daily rates, and status.</p>
            </div>
            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Register New Vehicle
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        modalOpen: false,
        vehicleName: '',
        licensePlate: '',
        images: [],
        currentIndex: 0,
        init() {
            this.$watch('modalOpen', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.classList.remove('overflow-hidden');
                    document.body.style.overflow = '';
                }
            });
        },
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

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('vehicles.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search Fleet</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Make, Model, License Plate..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status Filter</label>
                        <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="out_of_service" {{ request('status') === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-gray-900 dark:bg-indigo-600 hover:bg-gray-800 dark:hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow transition">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('vehicles.index') }}" class="py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 text-sm font-medium rounded-xl transition">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Vehicles Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse($vehicles as $vehicle)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col justify-between">
                        <div>
                            <!-- Header / Card Image Banner (Clickable to open Popup Carousel Gallery) -->
                            <div @click="openGallery('{{ addslashes($vehicle->name) }}', '{{ $vehicle->license_plate }}', {{ json_encode($vehicle->all_images) }})" class="h-40 bg-gradient-to-r from-slate-800 via-indigo-950 to-slate-900 relative overflow-hidden group cursor-pointer" title="Click to open vehicle photo gallery carousel">
                                @if($vehicle->image_path)
                                    <img src="{{ asset($vehicle->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $vehicle->name }}">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30"></div>
                                @endif
                                <div class="absolute inset-0 p-3.5 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-md {{ $vehicle->status === 'available' ? 'bg-emerald-500 text-white' : ($vehicle->status === 'maintenance' ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white') }}">
                                            {{ str_replace('_', ' ', $vehicle->status) }}
                                        </span>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="text-[10px] font-mono font-bold bg-black/60 backdrop-blur-md text-white px-2 py-0.5 rounded-lg border border-white/20 shadow-sm">
                                                {{ $vehicle->license_plate }}
                                            </span>
                                            <span class="text-[9px] font-extrabold bg-black/60 backdrop-blur-md text-white px-2 py-0.5 rounded-lg border border-white/20 shadow-sm flex items-center gap-1 group-hover:bg-indigo-600 transition">
                                                📷 {{ count($vehicle->all_images) ?: 1 }} {{ count($vehicle->all_images) > 1 ? 'Photos' : 'Photo' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-extrabold text-white leading-tight drop-shadow-md truncate">{{ $vehicle->name }}</h3>
                                        <p class="text-[11px] text-indigo-200 font-medium drop-shadow-sm">{{ $vehicle->year }} • {{ $vehicle->color ?? 'Standard Finish' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Vehicle Details Specs -->
                            <div class="p-4 space-y-3">
                                <!-- Vehicle Owner Badge -->
                                <div class="flex items-center justify-between p-2 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                                    <div class="flex items-center space-x-2 truncate">
                                        @if($vehicle->user?->avatar_url)
                                            <img src="{{ $vehicle->user->avatar_url }}" class="w-7 h-7 rounded-full object-cover shadow-xs flex-shrink-0 border border-indigo-300 dark:border-indigo-600">
                                        @else
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-[10px] flex items-center justify-center shadow-xs flex-shrink-0">
                                                {{ strtoupper(substr($vehicle->user?->formatted_name ?? 'A', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="truncate">
                                            <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block leading-none">Owner</span>
                                            <span class="text-xs font-extrabold text-gray-900 dark:text-gray-100 leading-tight block mt-0.5 truncate">{{ $vehicle->user?->formatted_name ?? 'Admin / System' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-1 py-1.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                                    <div>
                                        <span class="block text-[9px] text-gray-400 uppercase font-semibold">Trans.</span>
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->transmission }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[9px] text-gray-400 uppercase font-semibold">Fuel</span>
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->fuel_type }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[9px] text-gray-400 uppercase font-semibold">Seats</span>
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->seats }} Seats</span>
                                    </div>
                                </div>

                                <p class="text-[11px] text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ $vehicle->description ?? 'Standard rental vehicle specification with air conditioning, audio system, and clean interiors.' }}
                                </p>

                                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <div>
                                        <span class="text-[9px] uppercase font-bold tracking-wider text-indigo-600 dark:text-indigo-400 block">Daily Price</span>
                                        <div class="text-base font-black text-indigo-600 dark:text-indigo-400">
                                            ₱{{ number_format($vehicle->daily_rate, 2) }}
                                            <span class="text-[10px] font-normal text-gray-400">/day</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-gray-400 block">Bookings</span>
                                        <span class="block text-xs font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->bookings_count }} reserved</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="px-4 pb-4 pt-2 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex items-center gap-1.5">
                            <a href="{{ route('bookings.create', ['vehicle_id' => $vehicle->id]) }}" class="flex-1 py-1.5 px-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs text-center transition truncate">
                                Book Car
                            </a>
                            <a href="{{ route('calendar.index', ['vehicle_id' => $vehicle->id]) }}" title="View Calendar" class="p-2 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 hover:bg-amber-200 rounded-xl transition flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </a>
                            <a href="{{ route('vehicles.edit', $vehicle->id) }}" title="Edit Vehicle Data" class="p-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 rounded-xl transition flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 p-12 text-center rounded-2xl border border-gray-100 dark:border-gray-700">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">No Vehicles Found</h3>
                        <p class="text-gray-500 text-sm mt-1">Add your first rental car data entry with photo to start accepting bookings.</p>
                        <a href="{{ route('vehicles.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl text-sm shadow">
                            + Add Vehicle Data Entry
                        </a>
                    </div>
                @endforelse
            </div>

            @if(method_exists($vehicles, 'links') && $vehicles->hasPages())
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $vehicles->links() }}
                </div>
            @endif

        </div>

        <!-- Interactive Popup Carousel Modal for Vehicle Photos -->
        <div x-show="modalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @touchmove.prevent
             class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6 bg-slate-950/90 backdrop-blur-xl touch-none"
             style="display: none;">
            
            <div class="relative w-full max-w-4xl bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]" @click.away="closeModal()">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-800 bg-slate-900/80 backdrop-blur-md z-20">
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-white flex items-center gap-2">
                            <span>🚗</span>
                            <span x-text="vehicleName"></span>
                        </h3>
                        <p class="text-xs text-slate-400 font-mono">
                            License Plate: <span x-text="licensePlate" class="text-amber-400 font-bold"></span>
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <!-- Photo Counter -->
                        <span class="px-3 py-1 bg-slate-800 text-slate-300 font-mono text-xs font-bold rounded-full border border-slate-700">
                            Photo <span x-text="currentIndex + 1" class="text-emerald-400 font-extrabold"></span> of <span x-text="images.length"></span>
                        </span>
                        
                        <!-- Close Button -->
                        <button type="button" @click="closeModal()" class="p-2 text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-full transition shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Main Carousel Stage with Prev & Next Buttons -->
                <div class="relative flex-1 bg-black flex items-center justify-center min-h-[350px] sm:min-h-[480px] overflow-hidden group">
                    
                    <!-- Active Image Stack with Pure Smooth Opacity Transition -->
                    <template x-for="(img, idx) in images" :key="idx">
                        <div x-show="currentIndex === idx" 
                             x-transition:enter="transition-opacity ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition-opacity ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute inset-0 w-full h-full flex items-center justify-center p-3 sm:p-5">
                            <img :src="img" class="max-h-full max-w-full object-contain rounded-xl shadow-2xl" alt="Vehicle Photo">
                        </div>
                    </template>

                    <!-- Previous Button -->
                    <button type="button" 
                            x-show="images.length > 1"
                            @click.stop="prev()" 
                            class="absolute left-4 z-20 p-3 bg-black/60 hover:bg-black/90 text-white rounded-full backdrop-blur-md transition border border-white/20 shadow-xl focus:outline-none cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <!-- Next Button -->
                    <button type="button" 
                            x-show="images.length > 1"
                            @click.stop="next()" 
                            class="absolute right-4 z-20 p-3 bg-black/60 hover:bg-black/90 text-white rounded-full backdrop-blur-md transition border border-white/20 shadow-xl focus:outline-none cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <!-- Thumbnail Strip Selector -->
                <div x-show="images.length > 1" class="p-3 bg-slate-950 border-t border-slate-800 flex items-center justify-center gap-2 overflow-x-auto z-20">
                    <template x-for="(img, idx) in images" :key="idx">
                        <button type="button" 
                                @click.stop="currentIndex = idx" 
                                :class="currentIndex === idx ? 'ring-2 ring-emerald-500 opacity-100' : 'opacity-50 hover:opacity-100'" 
                                class="w-14 h-10 rounded-lg overflow-hidden border border-slate-700 transition flex-shrink-0 cursor-pointer">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
