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

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-8">
                
                <!-- Manage Existing Uploaded Vehicle Photos & Gallery -->
                @if(count($vehicle->all_images) > 0)
                    <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span>🖼️</span> {{ __('Current Photos Gallery & Cover Photo Management') }}
                                </h4>
                                <p class="text-xs text-gray-500">Delete unwanted photos or change which photo is assigned as the primary cover photo for fleet cards & bookings.</p>
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-mono text-xs font-bold rounded-lg border border-indigo-200 dark:border-indigo-800">
                                {{ count($vehicle->all_images) }} {{ count($vehicle->all_images) === 1 ? 'Photo' : 'Photos' }} Total
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($vehicle->all_images as $photo)
                                @php
                                    $isPrimary = ($vehicle->image_path === $photo || ltrim((string)$vehicle->image_path, '/') === ltrim($photo, '/'));
                                @endphp
                                <div class="relative group bg-white dark:bg-gray-800 rounded-xl overflow-hidden border {{ $isPrimary ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-gray-200 dark:border-gray-700' }} shadow-sm flex flex-col justify-between transition hover:shadow-md">
                                    <div class="h-32 bg-gray-900 relative overflow-hidden">
                                        <img src="{{ asset($photo) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                        @if($isPrimary)
                                            <div class="absolute top-2 left-2 px-2 py-0.5 bg-emerald-600 text-white text-[10px] font-black rounded-md shadow-md flex items-center gap-1">
                                                ★ Primary Cover
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-2 bg-gray-50 dark:bg-gray-800 flex items-center justify-between gap-1 border-t border-gray-100 dark:border-gray-700">
                                        @if(!$isPrimary)
                                            <form method="POST" action="{{ route('vehicles.set-primary-photo', $vehicle->id) }}" class="inline-block flex-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="image_path" value="{{ $photo }}">
                                                <button type="submit" title="Set as Primary Cover Photo" class="w-full py-1 px-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-extrabold text-[10px] rounded-lg transition border border-indigo-200 dark:border-indigo-800 text-center truncate">
                                                    ★ Make Primary
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 px-1 py-1">Active Cover</span>
                                        @endif

                                        <form method="POST" action="{{ route('vehicles.delete-photo', $vehicle->id) }}" onsubmit="return confirm('Are you sure you want to delete this photo from the vehicle entry?');" class="inline-block flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="image_path" value="{{ $photo }}">
                                            <button type="submit" title="Delete Photo" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-400 rounded-lg transition border border-rose-200 dark:border-rose-900">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
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

                    <!-- Photo Upload / Replace Inputs -->
                    <div class="space-y-4">
                        <x-input-label for="image" :value="__('1. Primary Cover Photo (PNG, JPG, WEBP - Max 5MB per file)')" />
                        <div class="flex flex-col md:flex-row items-center gap-4">
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
                            <div class="flex-1 w-full space-y-2">
                                <input type="file" id="image" name="image" accept="image/*" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300">
                                <p class="text-xs text-gray-400">Upload new primary photo to replace current image (Max 5MB).</p>
                                <x-input-error class="mt-1" :messages="$errors->get('image')" />
                            </div>
                        </div>

                        <!-- Multiple Gallery Photos Upload -->
                        <div class="pt-2">
                            <x-input-label for="images" :value="__('2. Add More Gallery Photos (Multiple Photos - Max 5MB per file)')" />
                            <input type="file" id="images" name="images[]" multiple accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/50 dark:file:text-emerald-300">
                            <p class="text-xs text-gray-400 mt-1">Add additional vehicle interior/exterior photos to the carousel gallery (Max 5MB each).</p>
                            <x-input-error class="mt-1" :messages="$errors->get('images')" />
                            <x-input-error class="mt-1" :messages="$errors->get('images.*')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Owner Select -->
                        <div>
                            <x-input-label for="user_id" :value="__('Vehicle Owner')" />
                            <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id', $vehicle->user_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>

                        <!-- Vehicle Type Select -->
                        <div>
                            <x-input-label for="vehicle_type_id" :value="__('Vehicle Type (Sedan, SUV, MPV, Van, Pickup)')" />
                            <select id="vehicle_type_id" name="vehicle_type_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold">
                                <option value="">-- Select Vehicle Type --</option>
                                @foreach($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}" {{ old('vehicle_type_id', $vehicle->vehicle_type_id) == $vt->id ? 'selected' : '' }}>
                                        🚗 {{ $vt->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('vehicle_type_id')" />
                        </div>

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
