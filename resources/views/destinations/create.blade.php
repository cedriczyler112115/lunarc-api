<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Add New Destination & Rental Price') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Add a Mindanao destination city/municipality and configure its destination rental fee.</p>
            </div>
            <a href="{{ route('destinations.index') }}" class="text-sm font-semibold text-sky-600 dark:text-sky-400 hover:underline">
                &larr; Back to Destinations List
            </a>
        </div>
    </x-slot>

    @php
        $mindanaoRegions = [
            'Region XIII (Caraga)',
            'Region X (Northern Mindanao)',
            'Region XI (Davao)',
            'Region XII (SOCCSKSARGEN)',
            'Region IX (Zamboanga Peninsula)',
            'BARMM',
        ];
    @endphp

    <div class="py-8" x-data="destinationCreator()">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-6">

                <form method="POST" action="{{ route('destinations.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Region Dropdown -->
                        <div>
                            <x-input-label for="region" :value="__('Region (Mindanao)')" />
                            <select id="region" name="region" x-model="selectedRegion" @change="onRegionChange()" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 p-3 font-semibold text-sm" required>
                                <option value="">-- Select Region --</option>
                                @foreach($mindanaoRegions as $reg)
                                    <option value="{{ $reg }}">{{ $reg }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('region')" />
                        </div>

                        <!-- Province Dropdown (Populated under Selected Region) -->
                        <div>
                            <x-input-label for="province" :value="__('Province')" />
                            
                            <template x-if="!isCustomProvince">
                                <select id="province" name="province" x-model="selectedProvince" @change="checkCustomProvince()" :disabled="!selectedRegion" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 p-3 font-semibold text-sm disabled:opacity-50" required>
                                    <option value="">-- Select Province --</option>
                                    <template x-for="prov in availableProvinces" :key="prov">
                                        <option :value="prov" x-text="prov"></option>
                                    </template>
                                    <option value="__NEW__">+ Add Custom / New Province</option>
                                </select>
                            </template>

                            <template x-if="isCustomProvince">
                                <div class="mt-1 flex space-x-2">
                                    <x-text-input id="custom_province_input" name="province" type="text" class="block w-full" x-model="customProvinceValue" required placeholder="Type new province name..." />
                                    <button type="button" @click="isCustomProvince = false; selectedProvince = '';" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold">Cancel</button>
                                </div>
                            </template>

                            <x-input-error class="mt-2" :messages="$errors->get('province')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- City / Municipality -->
                        <div>
                            <x-input-label for="city" :value="__('City / Municipality Name')" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full font-bold text-sm" :value="old('city')" required placeholder="e.g. Butuan City, General Luna, Davao City" />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <!-- Destination Rental Price -->
                        <div>
                            <x-input-label for="destination_rate" :value="__('Destination Rental Price / Fee (₱)')" />
                            <x-text-input id="destination_rate" name="destination_rate" type="number" step="0.01" min="0" class="mt-1 block w-full text-base font-extrabold text-sky-600 dark:text-sky-400" :value="old('destination_rate', '0.00')" required placeholder="0.00" />
                            <x-input-error class="mt-2" :messages="$errors->get('destination_rate')" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Description / Travel Route Notes')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl shadow-sm focus:ring-sky-500 focus:border-sky-500 text-sm" placeholder="e.g. Port entry destination or tourist spot note...">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('destinations.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 font-medium text-sm rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm rounded-xl shadow-md transition">
                            Save Destination & Price
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function destinationCreator() {
            return {
                selectedRegion: '{{ old("region", "Region XIII (Caraga)") }}',
                selectedProvince: '{{ old("province", "") }}',
                isCustomProvince: false,
                customProvinceValue: '',
                regionMap: @json($regionProvincesMap),

                get availableProvinces() {
                    if (this.selectedRegion && this.regionMap[this.selectedRegion]) {
                        return this.regionMap[this.selectedRegion];
                    }
                    return [];
                },

                onRegionChange() {
                    this.selectedProvince = '';
                    this.isCustomProvince = false;
                },

                checkCustomProvince() {
                    if (this.selectedProvince === '__NEW__') {
                        this.isCustomProvince = true;
                        this.customProvinceValue = '';
                    }
                }
            }
        }
    </script>
</x-app-layout>
