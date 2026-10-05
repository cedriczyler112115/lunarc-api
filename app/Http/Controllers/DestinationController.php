<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\DestinationVehicleRate;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DestinationController extends Controller
{
    /**
     * Display a listing of destinations with pricing management.
     */
    public function index(Request $request)
    {
        $query = Destination::query()->with('vehicleRates');

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('province', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $destinations = $query->orderBy('region')->orderBy('province')->orderBy('city')->paginate(15);
        $vehicleTypes = VehicleType::orderBy('created_at')->get();

        $regions = Destination::select('region')->distinct()->orderBy('region')->pluck('region');

        // Build map of Region => Array of Provinces
        $regionProvincesMap = Destination::select('region', 'province')
            ->distinct()
            ->orderBy('region')
            ->orderBy('province')
            ->get()
            ->groupBy('region')
            ->map(fn ($items) => $items->pluck('province')->values());

        return Inertia::render('Destinations/Index', [
            'destinations' => $destinations,
            'regions' => $regions,
            'regionProvincesMap' => $regionProvincesMap,
            'vehicleTypes' => $vehicleTypes,
            'filters' => $request->only(['region', 'province', 'search']),
        ]);
    }

    /**
     * Update destination rental rates for all vehicle types inline.
     */
    public function updateRates(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'rates' => 'nullable|array',
            'rates.*' => 'nullable|numeric|min:0',
            'destination_rate' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['destination_rate'])) {
            $destination->update(['destination_rate' => $validated['destination_rate']]);
        }

        if (! empty($validated['rates'])) {
            foreach ($validated['rates'] as $typeId => $rate) {
                if ($rate !== null && $rate !== '') {
                    DestinationVehicleRate::updateOrCreate(
                        [
                            'destination_id' => $destination->id,
                            'vehicle_type_id' => $typeId,
                        ],
                        [
                            'destination_rate' => (float) $rate,
                        ]
                    );
                }
            }
        }

        $msg = "Rental rates for {$destination->city} ({$destination->province}) updated successfully!";

        if (! $request->header('X-Inertia') && ($request->expectsJson() || $request->ajax())) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Update only the default destination rental price inline from table.
     */
    public function updatePrice(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'destination_rate' => 'required|numeric|min:0',
        ]);

        $destination->update($validated);

        return back()->with('success', "Rental price for {$destination->city} ({$destination->province}) updated to ₱".number_format($destination->destination_rate, 2));
    }

    /**
     * Show the form for creating a new destination entry.
     */
    public function create()
    {
        $regions = Destination::select('region')->distinct()->orderBy('region')->pluck('region');
        $vehicleTypes = VehicleType::orderBy('created_at')->get();

        $regionProvincesMap = Destination::select('region', 'province')
            ->distinct()
            ->orderBy('region')
            ->orderBy('province')
            ->get()
            ->groupBy('region')
            ->map(fn ($items) => $items->pluck('province')->unique()->values());

        return Inertia::render('Destinations/Create', [
            'regions' => $regions,
            'regionProvincesMap' => $regionProvincesMap,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    /**
     * Store a newly created destination entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'destination_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'rates' => 'nullable|array',
            'rates.*' => 'nullable|numeric|min:0',
        ]);

        $destination = Destination::create([
            'region' => $validated['region'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'destination_rate' => $validated['destination_rate'],
            'description' => $validated['description'] ?? null,
        ]);

        if (! empty($validated['rates'])) {
            foreach ($validated['rates'] as $typeId => $rate) {
                if ($rate !== null && $rate !== '') {
                    DestinationVehicleRate::create([
                        'destination_id' => $destination->id,
                        'vehicle_type_id' => $typeId,
                        'destination_rate' => (float) $rate,
                    ]);
                }
            }
        }

        return redirect()->route('destinations.index')
            ->with('success', "Destination {$destination->city} ({$destination->province}) added successfully with custom vehicle type rates!");
    }

    /**
     * Show the form for editing the specified destination rental price.
     */
    public function edit(Destination $destination)
    {
        $vehicleTypes = VehicleType::orderBy('created_at')->get();
        $destination->load('vehicleRates');

        return Inertia::render('Destinations/Edit', [
            'destination' => $destination,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    /**
     * Update the specified destination rental price in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'destination_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'rates' => 'nullable|array',
            'rates.*' => 'nullable|numeric|min:0',
        ]);

        $destination->update([
            'region' => $validated['region'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'destination_rate' => $validated['destination_rate'],
            'description' => $validated['description'] ?? null,
        ]);

        if (! empty($validated['rates'])) {
            foreach ($validated['rates'] as $typeId => $rate) {
                if ($rate !== null && $rate !== '') {
                    DestinationVehicleRate::updateOrCreate(
                        [
                            'destination_id' => $destination->id,
                            'vehicle_type_id' => $typeId,
                        ],
                        [
                            'destination_rate' => (float) $rate,
                        ]
                    );
                }
            }
        }

        return redirect()->route('destinations.index')
            ->with('success', "Rental prices for {$destination->city} ({$destination->province}) updated successfully!");
    }

    /**
     * Remove the specified destination entry from storage.
     */
    public function destroy(Destination $destination)
    {
        $name = "{$destination->city}, {$destination->province}";
        $destination->delete();

        return redirect()->route('destinations.index')
            ->with('success', "Destination {$name} deleted successfully.");
    }
}
