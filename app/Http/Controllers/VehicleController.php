<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vehicles = $query->withCount('bookings')->latest()->paginate(10);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate',
            'color' => 'nullable|string|max:100',
            'transmission' => 'required|in:Automatic,Manual',
            'fuel_type' => 'required|in:Gasoline,Diesel,Electric,Hybrid',
            'seats' => 'required|integer|min:1|max:50',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance,out_of_service',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vehicles', 'public');
            $validated['image_path'] = 'storage/' . $path;
        }

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', "Vehicle {$vehicle->name} registered successfully!");
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['bookings' => function ($q) {
            $q->orderBy('start_date', 'desc');
        }]);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate,' . $vehicle->id,
            'color' => 'nullable|string|max:100',
            'transmission' => 'required|in:Automatic,Manual',
            'fuel_type' => 'required|in:Gasoline,Diesel,Electric,Hybrid',
            'seats' => 'required|integer|min:1|max:50',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance,out_of_service',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($vehicle->image_path && str_contains($vehicle->image_path, 'storage/vehicles/')) {
                $relative = str_replace('storage/', '', $vehicle->image_path);
                Storage::disk('public')->delete($relative);
            }

            $path = $request->file('image')->store('vehicles', 'public');
            $validated['image_path'] = 'storage/' . $path;
        }

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', "Vehicle {$vehicle->name} updated successfully!");
    }

    public function destroy(Vehicle $vehicle)
    {
        $name = $vehicle->name;
        if ($vehicle->image_path && str_contains($vehicle->image_path, 'storage/vehicles/')) {
            $relative = str_replace('storage/', '', $vehicle->image_path);
            Storage::disk('public')->delete($relative);
        }
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', "Vehicle {$name} removed successfully!");
    }
}
