<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    /**
     * Display a paginated listing of completed booking incomes filtered by month and vehicle.
     */
    public function index(Request $request): Response
    {
        $userId = auth()->id();
        $userVehicleIds = Vehicle::where('user_id', $userId)->pluck('id');

        // Base query for user's completed bookings
        $baseQuery = Booking::where(function ($q) use ($userId, $userVehicleIds) {
            $q->where('user_id', $userId)
                ->orWhereIn('vehicle_id', $userVehicleIds);
        })->where('status', 'completed');

        // Apply Vehicle Filter
        if ($request->filled('vehicle_id')) {
            $baseQuery->where('vehicle_id', $request->vehicle_id);
        }

        // Apply Month Filter (format: YYYY-MM)
        if ($request->filled('month')) {
            try {
                $parsedDate = Carbon::createFromFormat('Y-m', $request->month);
                if ($parsedDate) {
                    $baseQuery->whereYear('start_date', $parsedDate->year)
                        ->whereMonth('start_date', $parsedDate->month);
                }
            } catch (\Exception $e) {
                // Ignore invalid month format
            }
        }

        // Calculate total income based on the filtered records
        $totalIncome = (int) (clone $baseQuery)->sum(DB::raw('COALESCE(actual_income, total_price, 0)'));
        $totalCompletedBookings = (clone $baseQuery)->count();

        // Paginated list with eager loaded relations
        $incomes = (clone $baseQuery)
            ->with(['vehicle', 'destinationModel'])
            ->orderBy('start_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        // List of user vehicles for filter dropdown
        $vehicles = Vehicle::where('user_id', $userId)
            ->orderBy('name')
            ->get(['id', 'name', 'license_plate', 'make', 'model']);

        // Collect available distinct months with completed bookings for easy dropdown selection
        $completedDates = Booking::where(function ($q) use ($userId, $userVehicleIds) {
            $q->where('user_id', $userId)
                ->orWhereIn('vehicle_id', $userVehicleIds);
        })
            ->where('status', 'completed')
            ->pluck('start_date');

        $availableMonths = $completedDates->map(function ($date) {
            $c = Carbon::parse($date);

            return [
                'month_val' => $c->format('Y-m'),
                'month_label' => $c->format('F Y'),
            ];
        })->unique('month_val')->values();

        return Inertia::render('Income/Index', [
            'incomes' => $incomes,
            'vehicles' => $vehicles,
            'availableMonths' => $availableMonths,
            'totalIncome' => $totalIncome,
            'totalCompletedBookings' => $totalCompletedBookings,
            'filters' => [
                'vehicle_id' => $request->vehicle_id ?? '',
                'month' => $request->month ?? '',
            ],
        ]);
    }
}
