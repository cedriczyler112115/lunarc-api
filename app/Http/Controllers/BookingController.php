<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['vehicle', 'destinationModel']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest()->paginate(10);
        $vehicles = Vehicle::orderBy('name')->get();

        return view('bookings.index', compact('bookings', 'vehicles'));
    }

    public function create(Request $request)
    {
        $vehicles = Vehicle::with(['vehicleType', 'bookings' => function ($q) {
            $q->whereIn('status', ['confirmed', 'pending', 'completed']);
        }])->where('status', 'available')->orderBy('name')->get();

        $destinations = Destination::with('vehicleRates')->orderBy('region')->orderBy('province')->orderBy('city')->get();
        
        $destinationsHierarchy = [];
        foreach ($destinations as $d) {
            $typeRates = [];
            foreach ($d->vehicleRates as $vr) {
                $typeRates[$vr->vehicle_type_id] = (float)$vr->destination_rate;
            }

            $destinationsHierarchy[$d->region][$d->province][] = [
                'id' => $d->id,
                'city' => $d->city,
                'base_rate' => (float)$d->destination_rate,
                'type_rates' => $typeRates,
                'description' => $d->description,
            ];
        }

        $selectedVehicleId = $request->query('vehicle_id');
        $startDate = $request->query('start_date', date('Y-m-d'));
        $endDate = $request->query('end_date', date('Y-m-d', strtotime('+1 day')));

        $selectedVehicle = $selectedVehicleId ? Vehicle::with('vehicleType')->find($selectedVehicleId) : null;

        return view('bookings.create', compact('vehicles', 'destinations', 'destinationsHierarchy', 'selectedVehicle', 'selectedVehicleId', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'destination_id' => 'required|exists:destinations,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'driver_license' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'reservation_fee' => 'nullable|numeric|min:0',
            'pickup_location' => 'required|string|max:255',
            'pickup_time' => 'required|string|max:255',
            'return_time' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::with('vehicleType')->findOrFail($validated['vehicle_id']);
        $destination = Destination::with('vehicleRates')->findOrFail($validated['destination_id']);

        // Check availability with time & 2-hour carwash buffer
        if (!$vehicle->isAvailableForDates($validated['start_date'], $validated['end_date'], null, $validated['pickup_time'] ?? null, $validated['return_time'] ?? null)) {
            return back()->withInput()->withErrors([
                'start_date' => "The selected vehicle ({$vehicle->name}) is unavailable for the requested schedule (a 2-hour carwash buffer is required after each return). Please choose a different date or time."
            ]);
        }

        $licensePath = null;
        if ($request->hasFile('driver_license')) {
            $path = $request->file('driver_license')->store('driver_licenses', 'public');
            $licensePath = 'storage/' . $path;
        }

        $pickupTimeStr = $validated['pickup_time'] ?? '00:00';
        $returnTimeStr = $validated['return_time'] ?? '00:00';

        $start = Carbon::parse($validated['start_date'] . ' ' . $pickupTimeStr);
        $end = Carbon::parse($validated['end_date'] . ' ' . $returnTimeStr);

        $totalMinutes = max(0, $start->diffInMinutes($end, false));
        $totalHours = (int)ceil($totalMinutes / 60.0);

        if ($totalHours <= 24) {
            $totalDays = 1;
            $excessHours = 0;
        } else {
            $fullDays = (int)floor($totalHours / 24);
            $remHours = $totalHours % 24;
            if ($remHours > 5) {
                $totalDays = $fullDays + 1;
                $excessHours = 0;
            } else {
                $totalDays = $fullDays;
                $excessHours = $remHours;
            }
        }
        
        // Calculate rates & total price: (Destination Fee x Days) + (Excess Hours x 200) - Reservation Fee
        $destinationRate = $destination->getRateForVehicleType($vehicle->vehicle_type_id);
        $reservationFee = (float)($validated['reservation_fee'] ?? 0);
        $excessFee = $excessHours * 200;
        $destinationSubtotal = ($totalDays * $destinationRate) + $excessFee;
        $totalPrice = max(0, $destinationSubtotal - $reservationFee);
        $destinationString = "{$destination->region} — {$destination->province} — {$destination->city}";

        $booking = Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'vehicle_id' => $vehicle->id,
            'user_id' => auth()->id(),
            'destination_id' => $destination->id,
            'destination' => $destinationString,
            'destination_rate' => $destinationRate,
            'reservation_fee' => $reservationFee,
            'customer_name' => $validated['customer_name'],
            'customer_email' => null,
            'customer_phone' => $validated['customer_phone'],
            'driver_license_path' => $licensePath,
            'pickup_location' => $validated['pickup_location'] ?? null,
            'pickup_time' => $validated['pickup_time'] ?? null,
            'return_time' => $validated['return_time'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'daily_rate' => $vehicle->daily_rate,
            'total_price' => $totalPrice,
            'status' => 'confirmed',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', "Booking {$booking->booking_code} created successfully!");
    }

    public function show(Booking $booking)
    {
        $booking->load('vehicle');
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $vehicles = Vehicle::all();
        return view('bookings.edit', compact('booking', 'vehicles'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', "Booking status updated to {$booking->status}.");
    }

    public function destroy(Booking $booking)
    {
        $code = $booking->booking_code;
        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', "Booking {$code} deleted successfully.");
    }
}
