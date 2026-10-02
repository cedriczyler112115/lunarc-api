<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('Destinations & Vehicle Type Rental Pricing') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Configure destination rates per vehicle type (Sedan, SUV, MPV, Van, Pickup, and custom vehicle types).</p>
            </div>
            <div>
                <a href="{{ route('destinations.create') }}" class="inline-flex items-center px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Add Destination
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="destinationFilter()">
        <!-- Floating Toaster Notifications Container -->
        <div class="fixed top-5 right-5 z-50 flex flex-col space-y-2 pointer-events-none max-w-sm w-full">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-show="true"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     :class="{
                         'bg-emerald-600 text-white shadow-emerald-500/20': toast.type === 'success',
                         'bg-rose-600 text-white shadow-rose-500/20': toast.type === 'error'
                     }"
                     class="pointer-events-auto flex items-center justify-between px-4 py-3 rounded-2xl shadow-xl border border-white/10 text-xs font-bold gap-3">
                    <div class="flex items-center space-x-2">
                        <template x-if="toast.type === 'success'">
                            <svg class="w-5 h-5 flex-shrink-0 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </template>
                        <span x-text="toast.message"></span>
                    </div>
                    <button type="button" @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-white/70 hover:text-white text-base leading-none font-bold">&times;</button>
                </div>
            </template>
        </div>

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

            <!-- Vehicle Types Pills Banner -->
            <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Configured Vehicle Types (Dynamic Rates Matrix)
                    </h3>
                    <span class="text-xs text-gray-400">Total: {{ $vehicleTypes->count() }} Vehicle Types</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($vehicleTypes as $vt)
                        <div class="inline-flex items-center space-x-2 px-3 py-1.5 bg-sky-50 dark:bg-sky-950/50 border border-sky-200 dark:border-sky-800 rounded-xl text-xs font-bold text-sky-900 dark:text-sky-200">
                            <span>🚗 {{ $vt->name }}</span>
                            @if(!in_array($vt->name, ['Sedan', 'SUV', 'MPV', 'Van', 'Pickup']))
                                <form method="POST" action="{{ route('vehicle-types.destroy', $vt->id) }}" class="inline-block" onsubmit="return confirm('Delete vehicle type {{ $vt->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Remove custom type" class="text-rose-500 hover:text-rose-700 ml-1 font-extrabold">&times;</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                    <button type="button" @click="showAddTypeModal = true" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 text-xs font-bold rounded-xl transition text-indigo-600 dark:text-indigo-400">
                        + Add Custom Vehicle Type
                    </button>
                </div>
            </div>

            <!-- Wired Search and Filter Bar -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('destinations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search City / Province / Region</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="City, Province or Region..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Region</label>
                        <select name="region" x-model="selectedRegion" @change="onRegionChange()" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold">
                            <option value="">All Regions</option>
                            @foreach($regions as $reg)
                                <option value="{{ $reg }}">{{ $reg }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Province</label>
                        <select name="province" x-model="selectedProvince" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold">
                            <option value="">All Provinces</option>
                            <template x-for="prov in availableProvinces" :key="prov">
                                <option :value="prov" x-text="prov" :selected="prov === selectedProvince"></option>
                            </template>
                        </select>
                    </div>

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

            <!-- Destinations Table Matrix with Per-Vehicle-Type Rates -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-4">Region</th>
                                <th class="px-4 py-4">Province</th>
                                <th class="px-4 py-4">City / Municipality</th>
                                
                                <!-- Dynamic Headers for each Vehicle Type -->
                                @foreach($vehicleTypes as $vt)
                                    <th class="px-3 py-4 text-center bg-sky-50/60 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300 border-l border-r border-sky-100 dark:border-sky-900/50">
                                        {{ $vt->name }}
                                        <span class="block text-[10px] font-semibold text-gray-400 lowercase">(rate ₱)</span>
                                    </th>
                                @endforeach

                                <th class="px-4 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($destinations as $dest)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-4 py-3 font-bold text-gray-800 dark:text-gray-200 text-xs">
                                        {{ $dest->region }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 text-xs">
                                        {{ $dest->province }}
                                    </td>
                                    <td class="px-4 py-3 font-extrabold text-gray-900 dark:text-white text-xs">
                                        📍 {{ $dest->city }}
                                    </td>

                                    <!-- Inline Rates Form for all Vehicle Types in this destination row -->
                                    <form method="POST" action="{{ route('destinations.update-rates', $dest->id) }}" data-dest-id="{{ $dest->id }}" @submit.prevent="saveRates($event)">
                                        @csrf
                                        @method('PATCH')

                                        @foreach($vehicleTypes as $vt)
                                            <td class="px-2 py-2 text-center border-l border-r border-gray-100 dark:border-gray-700 bg-slate-50/30 dark:bg-slate-900/20">
                                                <div class="relative w-28 mx-auto">
                                                    <span class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-[11px] font-black text-gray-400">₱</span>
                                                    <input type="number" step="0.01" min="0" name="rates[{{ $vt->id }}]" value="{{ number_format($dest->getRateForVehicleType($vt->id), 2, '.', '') }}" class="pl-5 pr-1 py-1 w-full text-xs font-black text-sky-600 dark:text-sky-400 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-sky-500 focus:border-sky-500 shadow-2xs">
                                                </div>
                                            </td>
                                        @endforeach

                                        <!-- Save Row Rates Action -->
                                        <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                                            <button type="submit" title="Save rates for all vehicle types" :disabled="savingDestId == '{{ $dest->id }}'" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-2xs transition inline-flex items-center gap-1 disabled:opacity-50">
                                                <template x-if="savingDestId == '{{ $dest->id }}'">
                                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                </template>
                                                <template x-if="savingDestId != '{{ $dest->id }}'">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </template>
                                                <span>Save</span>
                                            </button>
                                    </form>
                                        </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 4 + $vehicleTypes->count() }}" class="px-6 py-12 text-center text-gray-400">
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

        <!-- Add Vehicle Type Dynamic Modal -->
        <div x-show="showAddTypeModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.window.escape="showAddTypeModal = false"
             class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
             style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 max-w-md w-full p-6 space-y-4" @click.away="showAddTypeModal = false">
                <div class="flex items-center justify-between border-b pb-3 border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        🚗 {{ __('Add New Vehicle Type') }}
                    </h3>
                    <button type="button" @click="showAddTypeModal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('vehicle-types.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="vt_name" :value="__('Vehicle Type Name (e.g. Crossover, Luxury, Minibus)')" />
                        <x-text-input id="vt_name" name="name" type="text" class="mt-1 block w-full text-sm font-bold" required placeholder="e.g. Crossover, Truck, Minibus" />
                        <x-input-error class="mt-1" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="vt_description" :value="__('Description (Optional)')" />
                        <textarea id="vt_description" name="description" rows="2" class="mt-1 block w-full text-xs border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-xl" placeholder="e.g. Commercial heavy-duty or specialty vehicles"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" @click="showAddTypeModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow">
                            Add Dynamic Vehicle Type
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function destinationFilter() {
            return {
                showAddTypeModal: @json($errors->has('name')),
                selectedRegion: @json(request('region', '')),
                selectedProvince: @json(request('province', '')),
                regionMap: @json($regionProvincesMap),
                toasts: [],
                savingDestId: null,

                init() {
                    this.$watch('showAddTypeModal', value => {
                        if (value) {
                            document.body.classList.add('overflow-hidden');
                        } else {
                            document.body.classList.remove('overflow-hidden');
                        }
                    });
                },

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
                },

                showToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 3500);
                },

                async saveRates(event) {
                    const form = event.target;
                    const destId = form.dataset.destId;
                    this.savingDestId = destId;
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.showToast(data.message || 'Rates updated successfully!', 'success');
                        } else {
                            const errData = await response.json().catch(() => ({}));
                            this.showToast(errData.message || 'Failed to update rates.', 'error');
                        }
                    } catch (error) {
                        this.showToast('Network error while saving rates.', 'error');
                    } finally {
                        this.savingDestId = null;
                    }
                }
            }
        }
    </script>
</x-app-layout>
