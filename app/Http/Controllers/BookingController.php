<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $userVehicleIds = Vehicle::where('user_id', auth()->id())->pluck('id');

        $query = Booking::where(function ($q) use ($userVehicleIds) {
            $q->where('user_id', auth()->id())
                ->orWhereIn('vehicle_id', $userVehicleIds);
        })->with(['vehicle', 'destinationModel', 'user']);

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
        $vehicles = Vehicle::where('user_id', auth()->id())->orderBy('name')->get();

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'vehicles' => $vehicles,
            'filters' => $request->only(['vehicle_id', 'status', 'search']),
        ]);
    }

    public function create(Request $request)
    {
        $vehicles = Vehicle::with(['vehicleType', 'bookings' => function ($q) {
            $q->whereIn('status', ['confirmed', 'completed']);
        }])->where('status', 'available')->orderBy('name')->get();

        $destinations = Destination::with('vehicleRates')->orderBy('region')->orderBy('province')->orderBy('city')->get();

        $destinationsHierarchy = [];
        foreach ($destinations as $d) {
            $typeRates = [];
            foreach ($d->vehicleRates as $vr) {
                $typeRates[$vr->vehicle_type_id] = (float) $vr->destination_rate;
            }

            $destinationsHierarchy[$d->region][$d->province][] = [
                'id' => $d->id,
                'city' => $d->city,
                'base_rate' => (float) $d->destination_rate,
                'type_rates' => $typeRates,
                'description' => $d->description,
            ];
        }

        return Inertia::render('Bookings/Create', [
            'vehicles' => $vehicles,
            'destinations' => $destinations,
            'destinationsHierarchy' => $destinationsHierarchy,
            'preselectedVehicleId' => $request->query('vehicle_id'),
        ]);
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
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::with('vehicleType')->findOrFail($validated['vehicle_id']);
        if ($vehicle->status !== 'available') {
            return back()->withInput()->withErrors(['vehicle_id' => 'Vehicle is currently not available for booking.']);
        }

        $destination = Destination::with('vehicleRates')->findOrFail($validated['destination_id']);

        // Check availability with time & 2-hour carwash buffer
        $conflict = $vehicle->getConflictingBooking(
            $validated['start_date'],
            $validated['end_date'],
            null,
            $validated['pickup_time'] ?? null,
            $validated['return_time'] ?? null
        );

        if ($conflict) {
            $confCustomer = $conflict->customer_name ?: 'Another Customer';
            $confStart = $conflict->start_date->format('M d, Y').($conflict->pickup_time ? ' at '.Carbon::parse($conflict->pickup_time)->format('g:i A') : '');
            $confEnd = $conflict->end_date->format('M d, Y').($conflict->return_time ? ' at '.Carbon::parse($conflict->return_time)->format('g:i A') : '');

            return back()->withInput()->withErrors([
                'start_date' => "Schedule Conflict: Vehicle '{$vehicle->name}' ({$vehicle->license_plate}) already has a reservation (#{$conflict->booking_code} for {$confCustomer}) from {$confStart} to {$confEnd} (+2-hour carwash buffer). Please choose a different date or time.",
            ]);
        }

        $licensePath = null;
        if ($request->hasFile('driver_license')) {
            $path = $request->file('driver_license')->store('driver_licenses', 'public');
            $licensePath = 'storage/'.$path;
        }

        $pickupTimeStr = $validated['pickup_time'] ?? '00:00';
        $returnTimeStr = $validated['return_time'] ?? '00:00';

        $start = Carbon::parse($validated['start_date'].' '.$pickupTimeStr);
        $end = Carbon::parse($validated['end_date'].' '.$returnTimeStr);

        $totalMinutes = max(0, $start->diffInMinutes($end, false));
        $totalHours = (int) ceil($totalMinutes / 60.0);

        if ($totalHours <= 24) {
            $totalDays = 1;
            $excessHours = 0;
        } else {
            $fullDays = (int) floor($totalHours / 24);
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
        $reservationFee = (float) ($validated['reservation_fee'] ?? 0);
        $excessFee = $excessHours * 200;
        $destinationSubtotal = ($totalDays * $destinationRate) + $excessFee;
        $totalPrice = max(0, $destinationSubtotal - $reservationFee);
        $destinationString = "{$destination->city}, {$destination->province}";

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
        $booking->load(['vehicle.vehicleType', 'destinationModel', 'user']);

        return Inertia::render('Bookings/Show', [
            'booking' => $booking,
        ]);
    }

    public function edit(Booking $booking)
    {
        $booking->load(['vehicle.vehicleType', 'destinationModel']);

        $vehicles = Vehicle::with(['vehicleType', 'bookings' => function ($q) {
            $q->whereIn('status', ['confirmed', 'completed']);
        }])->orderBy('name')->get();

        $destinations = Destination::with('vehicleRates')->orderBy('region')->orderBy('province')->orderBy('city')->get();

        $destinationsHierarchy = [];
        foreach ($destinations as $d) {
            $typeRates = [];
            foreach ($d->vehicleRates as $vr) {
                $typeRates[$vr->vehicle_type_id] = (float) $vr->destination_rate;
            }

            $destinationsHierarchy[$d->region][$d->province][] = [
                'id' => $d->id,
                'city' => $d->city,
                'base_rate' => (float) $d->destination_rate,
                'type_rates' => $typeRates,
                'description' => $d->description,
            ];
        }

        $canEditAll = ! in_array($booking->status, ['completed', 'cancelled']);

        return Inertia::render('Bookings/Edit', [
            'booking' => [
                ...$booking->toArray(),
                'start_date' => $booking->start_date->format('Y-m-d'),
                'end_date' => $booking->end_date->format('Y-m-d'),
            ],
            'vehicles' => $vehicles,
            'destinations' => $destinations,
            'destinationsHierarchy' => $destinationsHierarchy,
            'canEditAll' => $canEditAll,
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Completed or cancelled bookings cannot be updated anymore.');
        }

        // If simple status update from Show page
        if ($request->has('status') && ! $request->has('start_date')) {
            $validated = $request->validate([
                'status' => 'required|in:confirmed,completed,cancelled',
                'actual_income' => 'nullable|integer|min:0',
                'notes' => 'nullable|string',
            ]);

            $updateData = [
                'status' => $validated['status'],
            ];

            if ($request->has('notes')) {
                $updateData['notes'] = $validated['notes'];
            }

            if ($validated['status'] === 'completed' && $request->filled('actual_income')) {
                $updateData['actual_income'] = (int) $validated['actual_income'];
            }

            $booking->update($updateData);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', "Booking status updated to {$booking->status}.");
        }

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
            'status' => 'nullable|in:confirmed,completed,cancelled',
            'actual_income' => 'nullable|integer|min:0',
        ]);

        $vehicle = Vehicle::with('vehicleType')->findOrFail($validated['vehicle_id']);
        $destination = Destination::with('vehicleRates')->findOrFail($validated['destination_id']);

        // Check availability with time & 2-hour carwash buffer, excluding current booking
        $conflict = $vehicle->getConflictingBooking(
            $validated['start_date'],
            $validated['end_date'],
            $booking->id,
            $validated['pickup_time'] ?? null,
            $validated['return_time'] ?? null
        );

        if ($conflict) {
            $confCustomer = $conflict->customer_name ?: 'Another Customer';
            $confStart = $conflict->start_date->format('M d, Y').($conflict->pickup_time ? ' at '.Carbon::parse($conflict->pickup_time)->format('g:i A') : '');
            $confEnd = $conflict->end_date->format('M d, Y').($conflict->return_time ? ' at '.Carbon::parse($conflict->return_time)->format('g:i A') : '');

            return back()->withInput()->withErrors([
                'start_date' => "Schedule Conflict: Vehicle '{$vehicle->name}' ({$vehicle->license_plate}) already has an active reservation (#{$conflict->booking_code} for {$confCustomer}) from {$confStart} to {$confEnd} (+2-hour carwash buffer). Please choose a different date or time.",
            ]);
        }

        $pickupTimeStr = $validated['pickup_time'] ?? '00:00';
        $returnTimeStr = $validated['return_time'] ?? '00:00';

        $start = Carbon::parse($validated['start_date'].' '.$pickupTimeStr);
        $end = Carbon::parse($validated['end_date'].' '.$returnTimeStr);

        $totalMinutes = max(0, $start->diffInMinutes($end, false));
        $totalHours = (int) ceil($totalMinutes / 60.0);

        if ($totalHours <= 24) {
            $totalDays = 1;
            $excessHours = 0;
        } else {
            $fullDays = (int) floor($totalHours / 24);
            $remHours = $totalHours % 24;
            if ($remHours > 5) {
                $totalDays = $fullDays + 1;
                $excessHours = 0;
            } else {
                $totalDays = $fullDays;
                $excessHours = $remHours;
            }
        }

        $destinationRate = $destination->getRateForVehicleType($vehicle->vehicle_type_id);
        $reservationFee = (float) ($validated['reservation_fee'] ?? 0);
        $excessFee = $excessHours * 200;
        $destinationSubtotal = ($totalDays * $destinationRate) + $excessFee;
        $totalPrice = max(0, $destinationSubtotal - $reservationFee);
        $destinationString = "{$destination->city}, {$destination->province}";

        $updateData = [
            'vehicle_id' => $vehicle->id,
            'destination_id' => $destination->id,
            'destination' => $destinationString,
            'destination_rate' => $destinationRate,
            'reservation_fee' => $reservationFee,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'pickup_location' => $validated['pickup_location'],
            'pickup_time' => $validated['pickup_time'],
            'return_time' => $validated['return_time'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'daily_rate' => $vehicle->daily_rate,
            'total_price' => $totalPrice,
            'notes' => $validated['notes'] ?? null,
        ];

        if (! empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
            if ($validated['status'] === 'completed' && $request->filled('actual_income')) {
                $updateData['actual_income'] = (int) $validated['actual_income'];
            }
        }

        if ($request->hasFile('driver_license')) {
            $path = $request->file('driver_license')->store('driver_licenses', 'public');
            $updateData['driver_license_path'] = 'storage/'.$path;
        }

        $booking->update($updateData);

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', "Booking {$booking->booking_code} updated successfully!");
    }

    public function destroy(Booking $booking)
    {
        $code = $booking->booking_code;
        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', "Booking {$code} deleted successfully.");
    }
}
