<section>
    <header class="flex items-center justify-between border-b pb-4 border-gray-100 dark:border-gray-700">
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <span>👤</span> {{ __('Profile & Personal Information') }}
            </h2>
            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                {{ __("Manage your basic account details, profile picture, and contact information.") }}
            </p>
        </div>
        <div>
            @if($user->isCarOwner())
                <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-extrabold text-xs rounded-full border border-indigo-200 dark:border-indigo-800">
                    🚗 Car Rental Owner
                </span>
            @else
                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs rounded-full border border-emerald-200 dark:border-emerald-800">
                    👤 Guest / Renter
                </span>
            @endif
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data="{ avatarPreview: null }">
        @csrf
        @method('patch')

        <!-- Profile Picture Avatar -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-2xl border border-gray-200 dark:border-gray-700 flex items-center gap-5">
            <div class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden flex-shrink-0 border-2 border-white dark:border-gray-500 shadow-md flex items-center justify-center relative">
                <template x-if="avatarPreview">
                    <img :src="avatarPreview" class="w-full h-full object-cover">
                </template>
                <template x-if="!avatarPreview">
                    @if($user->avatar_path)
                        <img src="{{ asset($user->avatar_path) }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-black text-2xl flex items-center justify-center">
                            {{ strtoupper(substr($user->first_name ?: $user->name, 0, 1)) }}
                        </div>
                    @endif
                </template>
            </div>
            <div class="flex-1 space-y-1">
                <x-input-label for="avatar" :value="__('Change Profile Picture')" />
                <input type="file" id="avatar" name="avatar" accept="image/*" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { avatarPreview = e.target.result; }; reader.readAsDataURL(file); }" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950 dark:file:text-indigo-300">
                <p class="text-[11px] text-gray-400">PNG, JPG, WEBP formats allowed (Max 5MB).</p>
                <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <!-- Basic Profile Names -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- First Name -->
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name ?: $user->name)" required autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <!-- Last Name -->
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>

            <!-- Middle Name -->
            <div>
                <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $user->middle_name)" autocomplete="additional-name" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>

            <!-- Extension Name -->
            <div>
                <x-input-label for="extension_name" :value="__('Extension Name (Optional)')" />
                <x-text-input id="extension_name" name="extension_name" type="text" class="mt-1 block w-full" :value="old('extension_name', $user->extension_name)" placeholder="e.g. Jr., Sr., III" />
                <x-input-error class="mt-2" :messages="$errors->get('extension_name')" />
            </div>
        </div>

        <!-- Contact & Birthday -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Birthday -->
            <div>
                <x-input-label for="birthday" :value="__('Date of Birth')" />
                <x-text-input id="birthday" name="birthday" type="date" class="mt-1 block w-full" :value="old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '')" required max="{{ date('Y-m-d') }}" />
                <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
            </div>

            <!-- Contact Number -->
            <div>
                <x-input-label for="contact_number" :value="__('Contact Mobile Number')" />
                <x-text-input id="contact_number" name="contact_number" type="text" class="mt-1 block w-full" :value="old('contact_number', $user->contact_number)" required placeholder="e.g. 09171234567" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
            </div>
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" :value="__('Complete Address')" />
            <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <!-- About Car Rental Owner (Required for Car Owners) -->
        <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50 space-y-2">
            <div class="flex items-center justify-between">
                <x-input-label for="owner_description" class="font-bold text-indigo-900 dark:text-indigo-300" :value="__('About Car Rental Owner / Rental Business Bio')" />
                @if($user->isCarOwner())
                    <span class="text-[10px] font-black text-rose-500 uppercase tracking-wider">* Required for Car Owners</span>
                @else
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">(Optional for Guests)</span>
                @endif
            </div>
            <textarea id="owner_description" name="owner_description" rows="3" {{ $user->isCarOwner() ? 'required' : '' }} class="block mt-1 w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Provide a brief description about your car rental business, fleet experience, or owner background...">{{ old('owner_description', $user->owner_description) }}</textarea>
            <x-input-error class="mt-1" :messages="$errors->get('owner_description')" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow">{{ __('Save Profile Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1"
                >✓ {{ __('Profile updated successfully.') }}</p>
            @endif
        </div>
    </form>
</section>
