<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight">
                {{ __('Edit Vehicle Data Entry: ') }} {{ $vehicle->name }}
            </h2>
            <a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Back to Vehicle List
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                
                <form method="POST" action="{{ route('vehicles.update', $vehicle->id) }}" enctype="multipart/form-data" class="space-y-6" x-data="{ imagePreview: null }">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center justify-between border-b pb-4 border-gray-100 dark:border-gray-700">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Vehicle Identification & Specs</h3>
                            <p class="text-xs text-gray-500">License Plate: <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->license_plate }}</span></p>
                        </div>
                        <a href="{{ route('vehicles.show', $vehicle->id) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 text-xs font-bold rounded-lg transition">
                            View Specs Page
                        </a>
                    </div>

                    <!-- Photo Upload / Replace Input -->
                    <div>
                        <x-input-label for="image" :value="__('Vehicle Photo (Upload new image to replace current photo)')" />
                        <div class="mt-2 flex flex-col md:flex-row items-center gap-4">
                            <!-- Current / New Image Preview Box -->
                            <div class="w-full md:w-48 h-32 rounded-2xl bg-gray-100 dark:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden relative">
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview">
                                    @if($vehicle->image_path)
                                        <img src="{{ asset($vehicle->image_path) }}" class="w-full h-full object-cover" alt="{{ $vehicle->name }}">
                                    @else
                                        <div class="text-center p-3 text-gray-400">
                                            <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-[10px] font-semibold block">No photo uploaded</span>
                                        </div>
                                    @endif
                                </template>
                            </div>

                            <!-- File Upload Control -->
                            <div class="flex-1 w-full">
                                <input type="file" id="image" name="image" accept="image/*" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300">
                                <p class="text-xs text-gray-400 mt-1">Leave blank if you want to keep the existing vehicle photo.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('image')" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Display Name -->
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Vehicle Name / Model Display Title')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $vehicle->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Make -->
                        <div>
                            <x-input-label for="make" :value="__('Manufacturer / Make')" />
                            <x-text-input id="make" name="make" type="text" class="mt-1 block w-full" :value="old('make', $vehicle->make)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('make')" />
                        </div>

                        <!-- Model -->
                        <div>
                            <x-input-label for="model" :value="__('Model Name')" />
                            <x-text-input id="model" name="model" type="text" class="mt-1 block w-full" :value="old('model', $vehicle->model)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('model')" />
                        </div>

                        <!-- Year -->
                        <div>
                            <x-input-label for="year" :value="__('Model Year')" />
                            <x-text-input id="year" name="year" type="number" class="mt-1 block w-full" :value="old('year', $vehicle->year)" required min="1990" max="{{ date('Y') + 1 }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('year')" />
                        </div>

                        <!-- License Plate -->
                        <div>
                            <x-input-label for="license_plate" :value="__('License Plate Number')" />
                            <x-text-input id="license_plate" name="license_plate" type="text" class="mt-1 block w-full" :value="old('license_plate', $vehicle->license_plate)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('license_plate')" />
                        </div>

                        <!-- Color -->
                        <div>
                            <x-input-label for="color" :value="__('Exterior Color')" />
                            <x-text-input id="color" name="color" type="text" class="mt-1 block w-full" :value="old('color', $vehicle->color)" />
                            <x-input-error class="mt-2" :messages="$errors->get('color')" />
                        </div>

                        <!-- Seats -->
                        <div>
                            <x-input-label for="seats" :value="__('Seating Capacity')" />
                            <x-text-input id="seats" name="seats" type="number" class="mt-1 block w-full" :value="old('seats', $vehicle->seats)" required min="1" max="50" />
                            <x-input-error class="mt-2" :messages="$errors->get('seats')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                        <!-- Daily Rate -->
                        <div>
                            <x-input-label for="daily_rate" :value="__('Daily Rental Rate (PHP ₱)')" />
                            <x-text-input id="daily_rate" name="daily_rate" type="number" step="0.01" class="mt-1 block w-full text-lg font-bold text-indigo-600" :value="old('daily_rate', $vehicle->daily_rate)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('daily_rate')" />
                        </div>

                        <!-- Transmission -->
                        <div>
                            <x-input-label for="transmission" :value="__('Transmission Type')" />
                            <select id="transmission" name="transmission" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="Automatic" {{ old('transmission', $vehicle->transmission) === 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                <option value="Manual" {{ old('transmission', $vehicle->transmission) === 'Manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('transmission')" />
                        </div>

                        <!-- Fuel Type -->
                        <div>
                            <x-input-label for="fuel_type" :value="__('Fuel Type')" />
                            <select id="fuel_type" name="fuel_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="Gasoline" {{ old('fuel_type', $vehicle->fuel_type) === 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                <option value="Diesel" {{ old('fuel_type', $vehicle->fuel_type) === 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="Hybrid" {{ old('fuel_type', $vehicle->fuel_type) === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                <option value="Electric" {{ old('fuel_type', $vehicle->fuel_type) === 'Electric' ? 'selected' : '' }}>Electric</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('fuel_type')" />
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-3">
                            <x-input-label for="status" :value="__('Operational Fleet Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="available" {{ old('status', $vehicle->status) === 'available' ? 'selected' : '' }}>Available (Ready for bookings)</option>
                                <option value="maintenance" {{ old('status', $vehicle->status) === 'maintenance' ? 'selected' : '' }}>Maintenance (Under service)</option>
                                <option value="out_of_service" {{ old('status', $vehicle->status) === 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Vehicle Features & Notes')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $vehicle->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('vehicles.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 font-medium text-sm rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                            Update Vehicle Entry & Photo
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
