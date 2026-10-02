<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <span class="font-mono text-indigo-600 dark:text-indigo-400">#{{ $booking->booking_code }}</span>
                    <span>Car Rental Reservation Receipt</span>
                </h2>
                <p class="text-sm text-gray-500">Booked on {{ $booking->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Back to Bookings List
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200 rounded-r-xl shadow-sm">
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Status Top Banner -->
                <div class="bg-slate-900 text-white p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Booking Status</span>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="px-3.5 py-1 text-xs font-black uppercase rounded-full {{ $booking->status === 'confirmed' ? 'bg-emerald-500 text-white' : ($booking->status === 'completed' ? 'bg-blue-500 text-white' : 'bg-amber-500 text-white') }}">
                                {{ $booking->status }}
                            </span>
                            <span class="text-sm text-slate-300">LunarC Car Rental - Butuan City</span>
                        </div>
                    </div>

                    <!-- Quick Status Updater Form -->
                    <form method="POST" action="{{ route('bookings.update', $booking->id) }}" class="flex items-center space-x-2">
                        @csrf
                        @method('PUT')
                        <select name="status" class="text-xs font-bold bg-slate-800 border-slate-700 text-white rounded-xl focus:ring-emerald-500 p-2">
                            <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Mark Confirmed</option>
                            <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Mark Completed</option>
                            <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Mark Pending</option>
                            <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Mark Cancelled</option>
                        </select>
                        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
                            Update Status
                        </button>
                    </form>
                </div>

                <div class="p-8 space-y-8">
                    <!-- Grid Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Booked Vehicle Card -->
                        <div class="p-6 bg-gray-50 dark:bg-gray-700/40 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Booked Vehicle Specs</span>
                            <h3 class="text-xl font-extrabold text-gray-900 dark:text-white">{{ $booking->vehicle->name ?? 'Vehicle' }}</h3>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                                <div>License Plate: <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $booking->vehicle->license_plate ?? 'N/A' }}</span></div>
                                <div>Year: <span class="font-bold text-gray-900 dark:text-white">{{ $booking->vehicle->year ?? '' }}</span></div>
                                <div>Transmission: <span class="font-bold text-gray-900 dark:text-white">{{ $booking->vehicle->transmission ?? '' }}</span></div>
                                <div>Capacity: <span class="font-bold text-gray-900 dark:text-white">{{ $booking->vehicle->seats ?? '' }} Seats</span></div>
                            </div>
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center text-xs">
                                <span class="text-gray-500">Daily Rate:</span>
                                <span class="font-bold text-gray-900 dark:text-white">₱{{ number_format($booking->daily_rate, 2) }} / day</span>
                            </div>
                        </div>

                        <!-- Customer Info Card -->
                        <div class="p-6 bg-gray-50 dark:bg-gray-700/40 rounded-2xl border border-gray-100 dark:border-gray-700 space-y-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Customer Contact Info</span>
                            <h3 class="text-xl font-extrabold text-gray-900 dark:text-white">{{ $booking->customer_name }}</h3>
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-300">
                                <p>Phone: <span class="font-bold text-gray-900 dark:text-white">{{ $booking->customer_phone }}</span></p>
                                <p>Email: <span class="font-bold text-gray-900 dark:text-white">{{ $booking->customer_email }}</span></p>
                            </div>
                            @if($booking->notes)
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-600 text-xs">
                                    <span class="text-gray-400 block font-semibold">Special Instructions:</span>
                                    <p class="text-gray-700 dark:text-gray-300 italic">{{ $booking->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                        <!-- Destination Info Card -->
                        <div class="p-6 bg-sky-50 dark:bg-sky-950/30 rounded-2xl border border-sky-100 dark:border-sky-800/60 space-y-3 col-span-1 md:col-span-2">
                            <span class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider block">Mindanao Destination & Rental Fee</span>
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white">{{ $booking->destination ?? 'Standard Car Rental' }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Destination Rate / Delivery Surcharge per City/Municipality</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500 uppercase block">Destination Fee</span>
                                    <span class="text-xl font-black text-sky-600 dark:text-sky-400">₱{{ number_format($booking->destination_rate, 2) }}</span>
                                </div>
                            </div>
                        </div>

                    <!-- Date Schedule & Cost Breakdown -->
                    <div class="p-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40 rounded-2xl space-y-4">
                        <h4 class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Rental Dates & Total Financial Summary</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center py-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-emerald-100 dark:border-emerald-900">
                            <div>
                                <span class="block text-xs text-gray-400 uppercase">Pickup Date</span>
                                <span class="text-lg font-extrabold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase">Return Date</span>
                                <span class="text-lg font-extrabold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase">Total Rental Period</span>
                                <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">{{ $booking->total_days }} {{ Str::plural('Day', $booking->total_days) }}</span>
                            </div>
                        </div>

                        <div class="space-y-2 pt-2 border-t border-emerald-200 dark:border-emerald-800/60 text-xs">
                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-300">
                                <span>Vehicle Daily Rate Subtotal ({{ $booking->total_days }} Days × ₱{{ number_format($booking->daily_rate, 2) }}):</span>
                                <span class="font-bold text-gray-900 dark:text-white">₱{{ number_format($booking->total_days * $booking->daily_rate, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-300">
                                <span>Destination Rental Fee ({{ $booking->destination }}):</span>
                                <span class="font-bold text-sky-600 dark:text-sky-400">+ ₱{{ number_format($booking->destination_rate, 2) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-emerald-300 dark:border-emerald-700">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Price Paid / Due:</span>
                            <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">₱{{ number_format($booking->total_price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Bottom Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                        <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-rose-100 text-rose-700 hover:bg-rose-200 text-xs font-bold rounded-xl transition">
                                Delete Booking Record
                            </button>
                        </form>

                        <div class="flex items-center space-x-3">
                            <a href="{{ route('calendar.index', ['vehicle_id' => $booking->vehicle_id]) }}" class="px-5 py-2.5 bg-amber-500 text-white font-bold text-xs rounded-xl shadow transition">
                                View in Booking Calendar
                            </a>
                            <a href="{{ route('bookings.index') }}" class="px-5 py-2.5 bg-gray-900 dark:bg-indigo-600 text-white font-bold text-xs rounded-xl shadow transition">
                                Back to All Bookings
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
