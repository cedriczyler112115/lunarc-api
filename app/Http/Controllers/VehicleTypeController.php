<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleTypeController extends Controller
{
    /**
     * Store a newly created dynamic vehicle type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:vehicle_types,name',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $vehicleType = VehicleType::create($validated);

        return redirect()->back()->with('success', "New vehicle type '{$vehicleType->name}' added successfully! You can now set destination rental rates for it.");
    }

    /**
     * Remove the specified dynamic vehicle type.
     */
    public function destroy(VehicleType $vehicleType)
    {
        $name = $vehicleType->name;
        $vehicleType->delete();

        return redirect()->back()->with('success', "Vehicle type '{$name}' removed.");
    }
}
