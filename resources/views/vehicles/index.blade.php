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

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($vehicles as $vehicle)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col justify-between">
                        <div>
                            <!-- Header / Card Image Banner -->
                            <div class="h-44 bg-gradient-to-r from-slate-800 via-indigo-950 to-slate-900 relative overflow-hidden group">
                                @if($vehicle->image_path)
                                    <img src="{{ asset($vehicle->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $vehicle->name }}">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/30"></div>
                                @endif
                                <div class="absolute inset-0 p-4 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-md {{ $vehicle->status === 'available' ? 'bg-emerald-500 text-white' : ($vehicle->status === 'maintenance' ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white') }}">
                                            {{ str_replace('_', ' ', $vehicle->status) }}
                                        </span>
                                        <span class="text-xs font-mono font-bold bg-black/60 backdrop-blur-md text-white px-2.5 py-1 rounded-lg border border-white/20 shadow-sm">
                                            {{ $vehicle->license_plate }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-extrabold text-white leading-tight drop-shadow-md">{{ $vehicle->name }}</h3>
                                        <p class="text-xs text-indigo-200 font-medium drop-shadow-sm">{{ $vehicle->year }} • {{ $vehicle->color ?? 'Standard Finish' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Vehicle Details Specs -->
                            <div class="p-5 space-y-4">
                                <!-- Vehicle Owner Badge -->
                                <div class="flex items-center justify-between p-2.5 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-xs flex items-center justify-center shadow-sm">
                                            {{ strtoupper(substr($vehicle->user?->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block leading-none">Vehicle Owner</span>
                                            <span class="text-xs font-extrabold text-gray-900 dark:text-gray-100 leading-tight block mt-0.5">{{ $vehicle->user?->name ?? 'Admin / System' }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-2xs">
                                        {{ $vehicle->user?->email ?? 'System Owner' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 gap-2 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                                    <div>
                                        <span class="block text-[10px] text-gray-400 uppercase font-semibold">Trans.</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->transmission }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-gray-400 uppercase font-semibold">Fuel</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->fuel_type }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-gray-400 uppercase font-semibold">Seats</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->seats }} Passengers</span>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ $vehicle->description ?? 'Standard rental vehicle specification with air conditioning, audio system, and clean interiors.' }}
                                </p>

                                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-indigo-600 dark:text-indigo-400 block">Starting Daily Price</span>
                                        <div class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                                            ₱{{ number_format($vehicle->daily_rate, 2) }}
                                            <span class="text-xs font-normal text-gray-400">/day</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-400">Bookings</span>
                                        <span class="block text-sm font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->bookings_count }} reserved</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="px-5 pb-5 pt-2 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <a href="{{ route('bookings.create', ['vehicle_id' => $vehicle->id]) }}" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm text-center transition">
                                Book Specific Car
                            </a>
                            <a href="{{ route('calendar.index', ['vehicle_id' => $vehicle->id]) }}" title="View Calendar" class="p-2 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 hover:bg-amber-200 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </a>
                            <a href="{{ route('vehicles.edit', $vehicle->id) }}" title="Edit Vehicle Data" class="p-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 rounded-xl transition">
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
    </div>
</x-app-layout>
