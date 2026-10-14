<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    /**
     * Search available vehicles based on vehicle type and rental dates/times.
     */
    public function index(Request $request): Response
    {
        $vehicleTypeId = $request->query('vehicle_type_id', '');
        $startDate = $request->query('start_date', '');
        $pickupTime = $request->query('pickup_time', '');
        $endDate = $request->query('end_date', '');
        $returnTime = $request->query('return_time', '');

        $hasSearched = ! empty($vehicleTypeId) || ! empty($startDate) || ! empty($endDate) || ! empty($pickupTime) || ! empty($returnTime);

        if (! $hasSearched) {
            $vehicles = collect();
        } else {
            $query = Vehicle::query()
                ->with(['user', 'vehicleType', 'bookings' => function ($q) {
                    $q->whereIn('status', ['confirmed', 'completed']);
                }])
                ->where('status', 'available');

            if (! empty($vehicleTypeId)) {
                $query->where('vehicle_type_id', $vehicleTypeId);
            }

            $vehicles = $query->orderBy('name')->get();

            // If dates are provided, filter out vehicles that conflict with existing bookings
            if (! empty($startDate) || ! empty($endDate)) {
                $effectiveStart = ! empty($startDate) ? $startDate : now()->format('Y-m-d');
                $effectiveEnd = ! empty($endDate) ? $endDate : $effectiveStart;

                if ($effectiveStart > $effectiveEnd) {
                    $temp = $effectiveStart;
                    $effectiveStart = $effectiveEnd;
                    $effectiveEnd = $temp;
                }

                $effectivePickupTime = ! empty($pickupTime) ? $pickupTime : '00:00';
                $effectiveReturnTime = ! empty($returnTime) ? $returnTime : '23:59';

                $vehicles = $vehicles->filter(function (Vehicle $vehicle) use ($effectiveStart, $effectiveEnd, $effectivePickupTime, $effectiveReturnTime) {
                    return $vehicle->isAvailableForDates($effectiveStart, $effectiveEnd, null, $effectivePickupTime, $effectiveReturnTime);
                })->values();
            }
        }

        $vehicleTypes = VehicleType::orderBy('name')->get();

        return Inertia::render('Search/Index', [
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'filters' => [
                'vehicle_type_id' => (string) $vehicleTypeId,
                'start_date' => (string) $startDate,
                'pickup_time' => (string) $pickupTime,
                'end_date' => (string) $endDate,
                'return_time' => (string) $returnTime,
            ],
            'hasSearched' => $hasSearched,
            'totalResults' => $vehicles->count(),
        ]);
    }
}
