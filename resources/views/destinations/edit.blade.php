<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('Edit Destination & Rental Price') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Update destination rental price and location details for {{ $destination->city }}, {{ $destination->province }}.</p>
            </div>
            <a href="{{ route('destinations.index') }}" class="text-sm font-semibold text-sky-600 dark:text-sky-400 hover:underline">
                &larr; Back to Destinations List
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-6">

                <form method="POST" action="{{ route('destinations.update', $destination->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Region -->
                        <div>
                            <x-input-label for="region" :value="__('Region (Mindanao)')" />
                            <x-text-input id="region" name="region" type="text" class="mt-1 block w-full" :value="old('region', $destination->region)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('region')" />
                        </div>

                        <!-- Province -->
                        <div>
                            <x-input-label for="province" :value="__('Province')" />
                            <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $destination->province)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('province')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- City / Municipality -->
                        <div>
                            <x-input-label for="city" :value="__('City / Municipality Name')" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $destination->city)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <!-- Destination Rental Price -->
                        <div>
                            <x-input-label for="destination_rate" :value="__('Destination Rental Price / Fee (₱)')" />
                            <x-text-input id="destination_rate" name="destination_rate" type="number" step="0.01" class="mt-1 block w-full text-lg font-extrabold text-sky-600 dark:text-sky-400" :value="old('destination_rate', $destination->destination_rate)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('destination_rate')" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Description / Travel Route Notes')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl shadow-sm focus:ring-sky-500 focus:border-sky-500 text-sm">{{ old('description', $destination->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('destinations.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 font-medium text-sm rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm rounded-xl shadow-md transition">
                            Update Destination Rental Price
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
