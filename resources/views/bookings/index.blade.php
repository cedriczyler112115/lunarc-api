<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    {{ __('My Bookings') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">View customer reservations, confirmed schedules, and status tracking.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('calendar.index') }}" class="inline-flex items-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-xl shadow transition duration-150">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Booking Calendar
                </a>
                <a href="{{ route('bookings.create') }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-md transition duration-150">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + New Car Booking
                </a>
            </div>
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

            <!-- Search and Filter Bar -->
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('bookings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search Customer / Code</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Booking Code, Customer Name..." class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Vehicle</label>
                        <select name="vehicle_id" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->license_plate }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Filter Status</label>
                        <select name="status" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Statuses</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-gray-900 dark:bg-emerald-600 hover:bg-gray-800 dark:hover:bg-emerald-700 text-white font-medium text-sm rounded-xl shadow transition">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['search', 'vehicle_id', 'status']))
                            <a href="{{ route('bookings.index') }}" class="py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl transition">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Bookings Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3.5 whitespace-nowrap">Booking Code</th>
                            <th class="px-4 py-3.5">Booked Vehicle</th>
                            <th class="px-4 py-3.5">Customer Info</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Dates & Duration</th>
                            <th class="px-3 py-3.5 whitespace-nowrap w-24 text-center">Total Amount</th>
                            <th class="px-3 py-3.5 whitespace-nowrap w-20 text-center">Status</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap w-12">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($bookings as $b)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <a href="{{ route('bookings.show', $b->id) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $b->booking_code }}
                                    </a>
                                    <div class="font-sans font-normal text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Booked by:<br>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $b->user->name ?? $b->customer_name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 dark:text-white leading-snug">{{ $b->vehicle->name ?? 'Deleted Vehicle' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $b->vehicle->license_plate ?? '' }} • ₱{{ number_format($b->daily_rate, 2) }}/day</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 dark:text-white leading-snug">{{ $b->customer_name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $b->customer_phone }} | {{ $b->customer_email }}</div>
                                    @if($b->destination)
                                        <div class="mt-1 text-[11px] font-bold text-sky-600 dark:text-sky-400 leading-tight">
                                            <span>📍 {{ $b->destination }}</span><br>
                                            @if($b->destination_rate > 0)
                                                <span class="text-[10px] text-gray-500 dark:text-gray-400 font-normal">
                                                    (₱{{ number_format($b->destination_rate, 2) }} / day)
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($b->start_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($b->end_date)->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                                        {{ $b->total_days }} {{ Str::plural('Day', $b->total_days) }}
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap w-24 text-center">
                                    <div class="font-extrabold text-sm text-gray-900 dark:text-white">
                                        ₱{{ number_format($b->total_price, 2) }}
                                    </div>
                                    @if(!is_null($b->actual_income))
                                        <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                                            Actual Income:<br>
                                            <span>₱{{ number_format($b->actual_income, 2) }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap w-20 text-center">
                                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-full {{ $b->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : ($b->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : ($b->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center whitespace-nowrap w-12">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <a href="{{ route('bookings.show', $b->id) }}" class="p-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 rounded-lg transition inline-flex items-center justify-center shadow-xs" title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('bookings.edit', $b->id) }}" class="p-1.5 bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-950/60 dark:text-amber-300 rounded-lg transition inline-flex items-center justify-center shadow-xs" title="Edit Booking">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        No bookings found matching your search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $bookings->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
