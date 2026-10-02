<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-gray-900 dark:text-white">Register Account</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Create your user account for LunarC Car Rental System</p>
    </div>

    <!-- Admin Approval Notice Banner -->
    <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-900 dark:bg-amber-950/40 dark:text-amber-200 rounded-r-xl text-xs space-y-1">
        <div class="font-extrabold uppercase tracking-wider flex items-center gap-1.5 text-amber-700 dark:text-amber-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Admin Approval Required</span>
        </div>
        <p class="leading-relaxed">
            All new user registrations must be reviewed and approved by an Administrator before login credentials become active.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5" x-data="{ role: '{{ old('role', 'guest') }}', avatarPreview: null }">
        @csrf

        <!-- Account Role Selection -->
        <div>
            <x-input-label :value="__('Select Account Type')" class="font-bold text-gray-900 dark:text-white" />
            <div class="grid grid-cols-2 gap-3 mt-2">
                <label :class="role === 'guest' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 ring-2 ring-emerald-500/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-3.5 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white">👤 Guest / Renter</span>
                        <input type="radio" name="role" value="guest" x-model="role" class="text-emerald-600 focus:ring-emerald-500">
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Can rent available vehicles and book trips</p>
                </label>

                <label :class="role === 'car_owner' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30 ring-2 ring-indigo-500/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'" class="p-3.5 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white">🚗 Car Rental Owner</span>
                        <input type="radio" name="role" value="car_owner" x-model="role" class="text-indigo-600 focus:ring-indigo-500">
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Owns vehicles and manages fleet rentals</p>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Profile Picture Upload -->
        <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden flex-shrink-0 border-2 border-white dark:border-gray-600 shadow-md flex items-center justify-center">
                <template x-if="avatarPreview">
                    <img :src="avatarPreview" class="w-full h-full object-cover">
                </template>
                <template x-if="!avatarPreview">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </template>
            </div>
            <div class="flex-1">
                <x-input-label for="avatar" :value="__('Profile Picture (Optional)')" />
                <input type="file" id="avatar" name="avatar" accept="image/*" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { avatarPreview = e.target.result; }; reader.readAsDataURL(file); }" class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950 dark:file:text-indigo-300">
                <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
            </div>
        </div>

        <!-- Personal Info Name Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- First Name -->
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" class="block mt-1 w-full text-sm" type="text" name="first_name" :value="old('first_name')" required placeholder="e.g. Maria" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
            </div>

            <!-- Last Name -->
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" class="block mt-1 w-full text-sm" type="text" name="last_name" :value="old('last_name')" required placeholder="e.g. Santos" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
            </div>

            <!-- Middle Name -->
            <div>
                <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
                <x-text-input id="middle_name" class="block mt-1 w-full text-sm" type="text" name="middle_name" :value="old('middle_name')" placeholder="e.g. Clara" />
                <x-input-error :messages="$errors->get('middle_name')" class="mt-1" />
            </div>

            <!-- Extension Name -->
            <div>
                <x-input-label for="extension_name" :value="__('Extension Name (Optional)')" />
                <x-text-input id="extension_name" class="block mt-1 w-full text-sm" type="text" name="extension_name" :value="old('extension_name')" placeholder="e.g. Jr., Sr., III" />
                <x-input-error :messages="$errors->get('extension_name')" class="mt-1" />
            </div>
        </div>

        <!-- Contact & Birthday Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Birthday -->
            <div>
                <x-input-label for="birthday" :value="__('Date of Birth')" />
                <x-text-input id="birthday" class="block mt-1 w-full text-sm" type="date" name="birthday" :value="old('birthday')" required max="{{ date('Y-m-d') }}" />
                <x-input-error :messages="$errors->get('birthday')" class="mt-1" />
            </div>

            <!-- Contact Number -->
            <div>
                <x-input-label for="contact_number" :value="__('Contact Mobile Number')" />
                <x-text-input id="contact_number" class="block mt-1 w-full text-sm" type="text" name="contact_number" :value="old('contact_number')" required placeholder="e.g. 09171234567" />
                <x-input-error :messages="$errors->get('contact_number')" class="mt-1" />
            </div>
        </div>

        <!-- Home Address -->
        <div>
            <x-input-label for="address" :value="__('Complete Address')" />
            <textarea id="address" name="address" rows="2" class="block mt-1 w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Street, Barangay, City, Province">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-1" />
        </div>

        <!-- About Car Rental Owner (Required for Car Owners) -->
        <div x-show="role === 'car_owner'" class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50 space-y-2">
            <div class="flex items-center justify-between">
                <x-input-label for="owner_description" class="font-bold text-indigo-900 dark:text-indigo-300" :value="__('About Car Rental Owner / Rental Business Bio')" />
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-wider">* Required for Car Owners</span>
            </div>
            <textarea id="owner_description" name="owner_description" rows="3" :required="role === 'car_owner'" class="block mt-1 w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Provide a brief description about your car rental business, fleet experience, or owner background...">{{ old('owner_description') }}</textarea>
            <x-input-error :messages="$errors->get('owner_description')" class="mt-1" />
        </div>

        <!-- Account Credentials -->
        <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Account Credentials</h3>
            
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input id="email" class="block mt-1 w-full text-sm" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="e.g. maria@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full text-sm" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4">
            <a class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('login') }}">
                {{ __('Already registered? Log in here') }}
            </a>

            <x-primary-button class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl shadow-md text-xs font-extrabold uppercase">
                {{ __('Submit Registration') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
