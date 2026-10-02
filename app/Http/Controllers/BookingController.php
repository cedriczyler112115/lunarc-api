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
        $vehicles = Vehicle::with(['bookings' => function ($q) {
            $q->whereIn('status', ['confirmed', 'pending', 'completed']);
        }])->where('status', 'available')->orderBy('name')->get();

        $destinations = Destination::orderBy('region')->orderBy('province')->orderBy('city')->get();
        
        $destinationsHierarchy = [];
        foreach ($destinations as $d) {
            $destinationsHierarchy[$d->region][$d->province][] = [
                'id' => $d->id,
                'city' => $d->city,
                'rate' => (float)$d->destination_rate,
                'description' => $d->description,
            ];
        }

        $selectedVehicleId = $request->query('vehicle_id');
        $startDate = $request->query('start_date', date('Y-m-d'));
        $endDate = $request->query('end_date', date('Y-m-d', strtotime('+1 day')));

        $selectedVehicle = $selectedVehicleId ? Vehicle::find($selectedVehicleId) : null;

        return view('bookings.create', compact('vehicles', 'destinations', 'destinationsHierarchy', 'selectedVehicle', 'selectedVehicleId', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'destination_id' => 'required|exists:destinations,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $destination = Destination::findOrFail($validated['destination_id']);

        // Check availability
        if (!$vehicle->isAvailableForDates($validated['start_date'], $validated['end_date'])) {
            return back()->withInput()->withErrors([
                'start_date' => "The selected vehicle ({$vehicle->name}) is already booked or unavailable between {$validated['start_date']} and {$validated['end_date']}. Please select different dates or another vehicle."
            ]);
        }

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = max(1, $start->diffInDays($end));
        $destinationRate = (float) $destination->destination_rate;
        $totalPrice = ($totalDays * (float) $vehicle->daily_rate) + $destinationRate;
        $destinationString = "{$destination->region} — {$destination->province} — {$destination->city}";

        $booking = Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'vehicle_id' => $vehicle->id,
            'user_id' => auth()->id(),
            'destination_id' => $destination->id,
            'destination' => $destinationString,
            'destination_rate' => $destinationRate,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
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
