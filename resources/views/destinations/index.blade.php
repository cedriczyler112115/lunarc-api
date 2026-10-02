<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('Destinations & Rental Price Management') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage Mindanao cities/municipalities and set destination rental prices inline (Vehicle daily rate serves as starting price).</p>
            </div>
            <a href="{{ route('destinations.create') }}" class="inline-flex items-center px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Add New Destination
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="destinationFilter()">
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

            <!-- Pricing Rule Explanation Banner -->
            <div class="p-5 bg-gradient-to-r from-sky-900 to-indigo-900 text-white rounded-2xl shadow-sm border border-sky-700 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-start space-x-3">
                    <div class="p-2.5 bg-sky-500 text-white rounded-xl flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-sky-200 uppercase tracking-wider">Destination Rental Pricing Logic</h4>
                        <p class="text-xs text-sky-100 mt-1 leading-relaxed">
                            Vehicle daily rates are treated as <strong>starting prices</strong>. Edit rental prices directly inline in the table below:
                            <span class="block mt-1 font-mono font-bold text-amber-300">Total Booking Price = (Vehicle Starting Daily Rate × Total Days) + Destination Rental Price</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Wired Search and Filter Bar (Region -> Province) -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('destinations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search City / Province / Region</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="City, Province or Region..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500">
                    </div>

                    <!-- Region Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Region</label>
                        <select name="region" x-model="selectedRegion" @change="onRegionChange()" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold">
                            <option value="">All Regions</option>
                            @foreach($regions as $reg)
                                <option value="{{ $reg }}">{{ $reg }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Wired Province Filter (Populated under Selected Region) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Province</label>
                        <select name="province" x-model="selectedProvince" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold">
                            <option value="">All Provinces</option>
                            <template x-for="prov in availableProvinces" :key="prov">
                                <option :value="prov" x-text="prov" :selected="prov === selectedProvince"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-xl shadow transition">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['search', 'region', 'province']))
                            <a href="{{ route('destinations.index') }}" class="py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Destinations Table with Inline Price Editing -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Region</th>
                                <th class="px-6 py-4">Province</th>
                                <th class="px-6 py-4">City / Municipality</th>
                                <th class="px-6 py-4">Inline Destination Rental Price (₱)</th>
                                <th class="px-6 py-4">Description / Notes</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($destinations as $dest)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200 text-xs">
                                        {{ $dest->region }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300 text-xs">
                                        {{ $dest->province }}
                                    </td>
                                    <td class="px-6 py-4 font-extrabold text-gray-900 dark:text-white">
                                        📍 {{ $dest->city }}
                                    </td>
                                    
                                    <!-- Inline Rental Price Edit Form -->
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('destinations.update-price', $dest->id) }}" class="flex items-center space-x-2">
                                            @csrf
                                            @method('PATCH')
                                            <div class="relative rounded-xl shadow-xs">
                                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs font-black text-gray-400">₱</span>
                                                <input type="number" step="0.01" min="0" name="destination_rate" value="{{ number_format($dest->destination_rate, 2, '.', '') }}" class="pl-6 pr-2 py-1.5 w-32 text-xs font-black text-sky-600 dark:text-sky-400 dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-xl focus:ring-sky-500 focus:border-sky-500 shadow-xs">
                                            </div>
                                            <button type="submit" title="Save updated rental price" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Save Price</span>
                                            </button>
                                        </form>
                                    </td>

                                    <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 italic">
                                        {{ $dest->description ?? 'Standard Mindanao route' }}
                                    </td>

                                    <!-- Actions Column (Edit Price button removed) -->
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('destinations.destroy', $dest->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete destination {{ $dest->city }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 text-xs font-bold rounded-lg transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                        No destinations found matching your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $destinations->links() }}
            </div>

        </div>
    </div>

    <script>
        function destinationFilter() {
            return {
                selectedRegion: @json(request('region', '')),
                selectedProvince: @json(request('province', '')),
                regionMap: @json($regionProvincesMap),

                get availableProvinces() {
                    if (this.selectedRegion && this.regionMap[this.selectedRegion]) {
                        return this.regionMap[this.selectedRegion];
                    }
                    let allProvs = [];
                    for (let r in this.regionMap) {
                        allProvs = allProvs.concat(this.regionMap[r]);
                    }
                    return [...new Set(allProvs)].sort();
                },

                onRegionChange() {
                    this.selectedProvince = '';
                }
            }
        }
    </script>
</x-app-layout>
