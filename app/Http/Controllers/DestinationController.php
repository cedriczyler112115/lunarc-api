<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of destinations with pricing management.
     */
    public function index(Request $request)
    {
        $query = Destination::query();

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
        
        $regions = Destination::select('region')->distinct()->orderBy('region')->pluck('region');
        
        // Build map of Region => Array of Provinces
        $regionProvincesMap = Destination::select('region', 'province')
            ->distinct()
            ->orderBy('region')
            ->orderBy('province')
            ->get()
            ->groupBy('region')
            ->map(fn($items) => $items->pluck('province')->values());

        return view('destinations.index', compact('destinations', 'regions', 'regionProvincesMap'));
    }

    /**
     * Update only the destination rental price inline from table.
     */
    public function updatePrice(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'destination_rate' => 'required|numeric|min:0',
        ]);

        $destination->update($validated);

        return back()->with('success', "Rental price for {$destination->city} ({$destination->province}) updated to ₱" . number_format($destination->destination_rate, 2));
    }

    /**
     * Show the form for creating a new destination entry.
     */
    public function create()
    {
        $regions = Destination::select('region')->distinct()->orderBy('region')->pluck('region');
        
        $regionProvincesMap = Destination::select('region', 'province')
            ->distinct()
            ->orderBy('region')
            ->orderBy('province')
            ->get()
            ->groupBy('region')
            ->map(fn($items) => $items->pluck('province')->unique()->values());

        return view('destinations.create', compact('regions', 'regionProvincesMap'));
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
        ]);

        $destination = Destination::create($validated);

        return redirect()->route('destinations.index')
            ->with('success', "Destination {$destination->city} ({$destination->province}) added successfully with rental price ₱" . number_format($destination->destination_rate, 2));
    }

    /**
     * Show the form for editing the specified destination rental price.
     */
    public function edit(Destination $destination)
    {
        return view('destinations.edit', compact('destination'));
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
        ]);

        $destination->update($validated);

        return redirect()->route('destinations.index')
            ->with('success', "Rental price for {$destination->city} ({$destination->province}) updated to ₱" . number_format($destination->destination_rate, 2));
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
