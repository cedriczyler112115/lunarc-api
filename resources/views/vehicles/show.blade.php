<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    {{ $vehicle->name }}
                    <span class="px-3 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $vehicle->status === 'available' ? 'bg-emerald-500 text-white' : ($vehicle->status === 'maintenance' ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white') }}">
                        {{ str_replace('_', ' ', $vehicle->status) }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">License Plate: <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $vehicle->license_plate }}</span></p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 text-sm font-semibold rounded-xl transition">
                    Edit Specs & Photo
                </a>
                <a href="{{ route('bookings.create', ['vehicle_id' => $vehicle->id]) }}" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow transition">
                    Book This Car
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden grid grid-cols-1 md:grid-cols-3">
                <!-- Vehicle Image Box -->
                <div class="md:col-span-1 bg-slate-900 relative min-h-[260px] flex items-center justify-center overflow-hidden">
                    @if($vehicle->image_path)
                        <img src="{{ asset($vehicle->image_path) }}" class="w-full h-full object-cover" alt="{{ $vehicle->name }}">
                    @else
                        <div class="text-center p-6 text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-bold block">No photo uploaded</span>
                            <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="mt-2 inline-block text-xs text-indigo-400 hover:underline">+ Upload Photo</a>
                        </div>
                    @endif
                </div>

                <!-- Vehicle Details & Rates -->
                <div class="md:col-span-2 p-8 space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Manufacturer / Model</span>
                                <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $vehicle->make }} {{ $vehicle->model }}</h3>
                                <p class="text-xs text-indigo-500 font-semibold">{{ $vehicle->year }} Model • {{ $vehicle->color ?? 'Standard Finish' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-400 uppercase font-semibold">Daily Rate</span>
                                <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400">
                                    ₱{{ number_format($vehicle->daily_rate, 2) }}
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Owner Pill -->
                        <div class="flex items-center justify-between p-3.5 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-2xl border border-indigo-100 dark:border-indigo-900/50">
                            <div class="flex items-center space-x-3">
                                @if($vehicle->user?->avatar_url)
                                    <img src="{{ $vehicle->user->avatar_url }}" class="w-10 h-10 rounded-full object-cover shadow-md border-2 border-indigo-400 dark:border-indigo-600">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-sm flex items-center justify-center shadow border-2 border-white">
                                        {{ strtoupper(substr($vehicle->user?->formatted_name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <span class="text-[10px] font-extrabold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block leading-none">Vehicle Registered Owner</span>
                                    <span class="text-sm font-black text-gray-900 dark:text-white block mt-1">{{ $vehicle->user?->formatted_name ?? 'Admin / System Owner' }}</span>
                                    @if($vehicle->user?->owner_description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic leading-tight">"{{ $vehicle->user->owner_description }}"</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block">{{ $vehicle->user?->email ?? 'admin@example.com' }}</span>
                                @if($vehicle->user?->contact_number)
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 block mt-0.5">📞 {{ $vehicle->user->contact_number }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Specs Pills -->
                        <div class="grid grid-cols-3 gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold">Transmission</span>
                                <span class="text-sm font-extrabold text-gray-800 dark:text-gray-200">{{ $vehicle->transmission }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold">Fuel Type</span>
                                <span class="text-sm font-extrabold text-gray-800 dark:text-gray-200">{{ $vehicle->fuel_type }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold">Seats</span>
                                <span class="text-sm font-extrabold text-gray-800 dark:text-gray-200">{{ $vehicle->seats }} Passengers</span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Features & Notes</span>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $vehicle->description ?? 'Standard rental vehicle specs with full air conditioning, power steering, and clean interior.' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <a href="{{ route('calendar.index', ['vehicle_id' => $vehicle->id]) }}" class="text-xs font-bold text-amber-600 hover:underline flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            View Booking Calendar for {{ $vehicle->name }}
                        </a>
                        <a href="{{ route('vehicles.index') }}" class="text-xs text-gray-500 hover:underline">&larr; Back to Vehicles</a>
                    </div>
                </div>
            </div>

            <!-- Booking History for this vehicle -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700">
                    Reservation History for {{ $vehicle->name }}
                </h3>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($vehicle->bookings as $bk)
                        <div class="py-3 flex items-center justify-between text-sm">
                            <div>
                                <span class="font-mono font-bold text-xs px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">{{ $bk->booking_code }}</span>
                                <span class="font-bold text-gray-900 dark:text-white ml-2">{{ $bk->customer_name }}</span>
                                <span class="text-xs text-gray-400 block mt-0.5">
                                    {{ \Carbon\Carbon::parse($bk->start_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($bk->end_date)->format('M d, Y') }} ({{ $bk->total_days }} days)
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full uppercase {{ $bk->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $bk->status }}
                                </span>
                                <span class="block text-xs font-black text-gray-900 dark:text-white mt-0.5">₱{{ number_format($bk->total_price, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-4">No reservations for this vehicle yet.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
