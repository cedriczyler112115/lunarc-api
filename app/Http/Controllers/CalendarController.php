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
    public function index(Request $request)
    {
        $selectedMonth = $request->query('month', date('Y-m'));
        $selectedVehicleId = $request->query('vehicle_id');

        if ($request->has('user_id')) {
            $selectedUserId = $request->query('user_id');
        } else {
            $selectedUserId = auth()->check() ? (string) auth()->id() : null;
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

        $vehiclesQuery = Vehicle::query();
        if ($selectedUserId && $selectedUserId !== 'all') {
            $vehiclesQuery->where('user_id', $selectedUserId);
        }

        $allVehicles = (clone $vehiclesQuery)->orderBy('name')->get();

        if ($selectedVehicleId) {
            $vehiclesQuery->where('id', $selectedVehicleId);
        }
        $vehicles = $vehiclesQuery->orderBy('name')->get();

        // Get bookings overlapping with calendar window
        $bookingsQuery = Booking::with(['vehicle', 'user', 'destinationModel'])
            ->whereIn('status', ['confirmed', 'pending', 'completed']);

        if ($selectedVehicleId) {
            $bookingsQuery->where('vehicle_id', $selectedVehicleId);
        }

        if ($selectedUserId && $selectedUserId !== 'all') {
            $bookingsQuery->where(function ($q) use ($selectedUserId) {
                $q->where('user_id', $selectedUserId)
                    ->orWhereHas('vehicle', function ($vq) use ($selectedUserId) {
                        $vq->where('user_id', $selectedUserId);
                    });
            });
        }

        $bookings = $bookingsQuery->where(function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('start_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->orWhereBetween('end_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->orWhere(function ($sub) use ($startOfWeek, $endOfWeek) {
                    $sub->where('start_date', '<=', $startOfWeek->format('Y-m-d'))
                        ->where('end_date', '>=', $endOfWeek->format('Y-m-d'));
                });
        })
            ->get()
            ->map(fn (Booking $booking): array => [
                ...$booking->toArray(),
                'start_date' => $booking->start_date->format('Y-m-d'),
                'end_date' => $booking->end_date->format('Y-m-d'),
                'destination_label' => $this->destinationLabel($booking),
            ])
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
