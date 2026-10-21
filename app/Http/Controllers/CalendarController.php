<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public const DISTINCT_BOOKING_COLORS = [
        '#2563eb', // 1: Royal Blue
        '#dc2626', // 2: Crimson Red
        '#16a34a', // 3: Kelly Green
        '#ea580c', // 4: Vivid Orange
        '#9333ea', // 5: Electric Purple
        '#eab308', // 6: Sunny Yellow
        '#ec4899', // 7: Hot Pink
        '#0891b2', // 8: Cyan / Turquoise
        '#0f172a', // 9: Dark Slate / Charcoal
        '#854d0e', // 10: Warm Saddle Brown
        '#65a30d', // 11: Chartreuse Lime
        '#4338ca', // 12: Deep Indigo
        '#0d9488', // 13: Bright Teal
        '#c026d3', // 14: Fuchsia
        '#be123c', // 15: Ruby Rose
        '#047857', // 16: Pine / Forest Green
    ];

    public function index(Request $request)
    {
        $selectedMonth = $request->query('month', date('Y-m'));
        $selectedVehicleId = $request->query('vehicle_id');

        $currentUserId = auth()->check() ? (string) auth()->id() : null;

        if ($request->has('user_id')) {
            $selectedUserId = $request->query('user_id');
        } else {
            $selectedUserId = $currentUserId;
        }

        try {
            $currentDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        } catch (\Exception $e) {
            $currentDate = Carbon::now()->startOfMonth();
            $selectedMonth = $currentDate->format('Y-m');
        }

        $prevMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');

        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Build calendar grid days (including trailing days from prev month and leading days from next month)
        $startOfWeek = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $allUsers = User::orderBy('name')->get();

        $vehiclesQuery = Vehicle::with('user');
        if ($selectedUserId && $selectedUserId !== 'all') {
            $vehiclesQuery->where('user_id', $selectedUserId);
        }

        $allVehicles = (clone $vehiclesQuery)->orderBy('name')->get();

        if ($selectedVehicleId) {
            $vehiclesQuery->where('id', $selectedVehicleId);
        }
        $vehicles = $vehiclesQuery->orderBy('name')->get();

        // Get bookings overlapping with calendar window
        $bookingsQuery = Booking::with(['vehicle.user', 'user', 'destinationModel'])
            ->whereIn('status', ['confirmed', 'completed']);

        if ($selectedVehicleId) {
            $bookingsQuery->where('vehicle_id', $selectedVehicleId);
        }

        if ($selectedUserId && $selectedUserId !== 'all') {
            $isFilteringOtherUser = $currentUserId !== null && strtolower((string) $selectedUserId) !== strtolower((string) $currentUserId);
            $isSpecialUser = $currentUserId !== null && strtolower((string) $currentUserId) === '01a0fa43-0b48-7050-906c-71f7809aa390';

            if ($isFilteringOtherUser && ! $isSpecialUser) {
                $bookingsQuery->where('user_id', $currentUserId)
                    ->whereHas('vehicle', function ($vq) use ($selectedUserId) {
                        $vq->where('user_id', $selectedUserId);
                    });
            } else {
                $bookingsQuery->where(function ($q) use ($selectedUserId) {
                    $q->where('user_id', $selectedUserId)
                        ->orWhereHas('vehicle', function ($vq) use ($selectedUserId) {
                            $vq->where('user_id', $selectedUserId);
                        });
                });
            }
        }

        $rawBookings = $bookingsQuery->where(function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('start_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->orWhereBetween('end_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->orWhere(function ($sub) use ($startOfWeek, $endOfWeek) {
                    $sub->where('start_date', '<=', $startOfWeek->format('Y-m-d'))
                        ->where('end_date', '>=', $endOfWeek->format('Y-m-d'));
                });
        })->get();

        $vehicleIds = $rawBookings->pluck('vehicle_id')->filter()->unique();

        // Get chronological sequence of all bookings for these vehicles to keep colors permanently consistent across months
        $allVehicleBookings = Booking::whereIn('vehicle_id', $vehicleIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderBy('start_date')
            ->orderBy('id')
            ->get(['id', 'vehicle_id'])
            ->groupBy('vehicle_id');

        $bookingColorMap = [];
        foreach ($allVehicleBookings as $vId => $vBks) {
            $vSeed = abs(crc32((string) $vId));
            $baseOffset = ($vSeed * 2) % count(self::DISTINCT_BOOKING_COLORS);

            foreach ($vBks as $idx => $b) {
                $colorIdx = ($baseOffset + $idx) % count(self::DISTINCT_BOOKING_COLORS);
                $bookingColorMap[$b->id] = self::DISTINCT_BOOKING_COLORS[$colorIdx];
            }
        }

        $currentUserId = auth()->id();

        $bookings = $rawBookings
            ->map(function (Booking $booking) use ($bookingColorMap, $currentUserId): array {
                $isOwner = $currentUserId !== null && (
                    (string) $booking->user_id === (string) $currentUserId
                    || (string) $booking->vehicle?->user_id === (string) $currentUserId
                    || (auth()->check() && auth()->user()->is_admin)
                );
                $data = $booking->toArray();
                if (! $isOwner) {
                    $data['customer_phone'] = null;
                }

                return [
                    ...$data,
                    'is_owner' => $isOwner,
                    'start_date' => $booking->start_date->format('Y-m-d'),
                    'end_date' => $booking->end_date->format('Y-m-d'),
                    'destination_label' => $this->destinationLabel($booking),
                    'line_color' => $bookingColorMap[$booking->id] ?? self::DISTINCT_BOOKING_COLORS[0],
                ];
            })
            ->values();

        return Inertia::render('Calendar/Index', [
            'currentDate' => $currentDate->format('Y-m-d'),
            'selectedMonth' => $selectedMonth,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'startOfWeek' => $startOfWeek->format('Y-m-d'),
            'endOfWeek' => $endOfWeek->format('Y-m-d'),
            'vehicles' => $vehicles,
            'allVehicles' => $allVehicles,
            'selectedVehicleId' => $selectedVehicleId,
            'allUsers' => $allUsers,
            'selectedUserId' => $selectedUserId,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Format a booking destination as "Municipality, Province" for display.
     */
    private function destinationLabel(Booking $booking): string
    {
        if ($booking->destinationModel) {
            return $booking->destinationModel->city.', '.$booking->destinationModel->province;
        }

        if (! empty($booking->destination)) {
            $cleanDestination = preg_replace('/^Region\s+[^-\n\:]*?(\([^)]*\))?\s*[-:]?\s*/i', '', $booking->destination);

            return trim((string) $cleanDestination) ?: $booking->destination;
        }

        return 'Standard Rental';
    }
}
