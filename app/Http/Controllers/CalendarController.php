<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller

{
    public function index(Request $request)
    {
        $selectedMonth = $request->query('month', date('Y-m'));
        $selectedVehicleId = $request->query('vehicle_id');

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

        $vehiclesQuery = Vehicle::query();
        if ($selectedVehicleId) {
            $vehiclesQuery->where('id', $selectedVehicleId);
        }
        $vehicles = $vehiclesQuery->orderBy('name')->get();

        $allVehicles = Vehicle::orderBy('name')->get();

        // Get bookings overlapping with calendar window (filtered by selected vehicle if present)
        $bookingsQuery = Booking::with('vehicle')
            ->whereIn('status', ['confirmed', 'pending', 'completed']);

        if ($selectedVehicleId) {
            $bookingsQuery->where('vehicle_id', $selectedVehicleId);
        }

        $bookings = $bookingsQuery->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('start_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                  ->orWhere(function ($sub) use ($startOfWeek, $endOfWeek) {
                      $sub->where('start_date', '<=', $startOfWeek->format('Y-m-d'))
                          ->where('end_date', '>=', $endOfWeek->format('Y-m-d'));
                  });
            })
            ->get();


        return view('calendar.index', compact(
            'currentDate',
            'selectedMonth',
            'prevMonth',
            'nextMonth',
            'startOfWeek',
            'endOfWeek',
            'vehicles',
            'allVehicles',
            'selectedVehicleId',
            'bookings'
        ));
    }
}

