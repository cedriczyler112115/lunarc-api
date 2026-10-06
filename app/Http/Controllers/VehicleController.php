<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()
            ->where('user_id', auth()->id())
            ->with(['user', 'vehicleType']);

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

        $vehicles = $query->withCount('bookings')->latest()->get();

        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function allListings(Request $request)
    {
        $query = Vehicle::query()->with(['user', 'vehicleType'])->withCount('bookings');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        if ($request->filled('seats')) {
            $query->where('seats', '>=', (int) $request->seats);
        }

        if ($request->filled('min_price')) {
            $query->where('daily_rate', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('daily_rate', '<=', (float) $request->max_price);
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('daily_rate', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('daily_rate', 'desc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'seats_desc') {
            $query->orderBy('seats', 'desc');
        } else {
            $query->latest();
        }

        $vehicles = $query->get();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $totalFleetCount = Vehicle::count();
        $availableFleetCount = Vehicle::where('status', 'available')->count();

        return Inertia::render('Vehicles/AllListing', [
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'totalFleetCount' => $totalFleetCount,
            'availableFleetCount' => $availableFleetCount,
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        $users = User::where('is_approved', true)->orderBy('name')->get();
        $vehicleTypes = VehicleType::orderBy('name')->get();

        return Inertia::render('Vehicles/Create', [
            'users' => $users,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('license_plate')) {
            $request->merge(['license_plate' => trim(strtoupper((string) $request->input('license_plate')))]);
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'name' => 'required|string|max:255',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:'.(date('Y') + 1),
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate',
            'color' => 'nullable|string|max:100',
            'transmission' => 'required|in:Automatic,Manual',
            'fuel_type' => 'required|in:Gasoline,Diesel,Electric,Hybrid',
            'seats' => 'required|integer|min:1|max:50',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance,out_of_service',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'images' => 'nullable|array|max:15',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'license_plate.unique' => 'The plate number is already registered. Each vehicle must have a unique plate number owned by only 1 user.',
        ]);

        if (empty($validated['user_id'])) {
            $validated['user_id'] = auth()->id();
        }

        $allUploadedImages = [];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vehicles', 'public');
            $validated['image_path'] = 'storage/'.$path;
            $allUploadedImages[] = 'storage/'.$path;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('vehicles', 'public');
                $allUploadedImages[] = 'storage/'.$path;
            }
        }

        if (! empty($allUploadedImages)) {
            if (empty($validated['image_path'])) {
                $validated['image_path'] = $allUploadedImages[0];
            }
            $validated['images'] = array_values(array_unique($allUploadedImages));
        }

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', "Vehicle {$vehicle->name} registered successfully!");
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['user', 'vehicleType', 'bookings' => function ($q) {
            $q->with('destinationModel')->orderBy('start_date', 'desc');
        }]);

        return Inertia::render('Vehicles/Show', [
            'vehicle' => $vehicle,
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        $users = User::where('is_approved', true)->orderBy('name')->get();
        $vehicleTypes = VehicleType::orderBy('name')->get();

        return Inertia::render('Vehicles/Edit', [
            'vehicle' => $vehicle,
            'users' => $users,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        if ($request->has('license_plate')) {
            $request->merge(['license_plate' => trim(strtoupper((string) $request->input('license_plate')))]);
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'name' => 'required|string|max:255',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:'.(date('Y') + 1),
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate,'.$vehicle->id,
            'color' => 'nullable|string|max:100',
            'transmission' => 'required|in:Automatic,Manual',
            'fuel_type' => 'required|in:Gasoline,Diesel,Electric,Hybrid',
            'seats' => 'required|integer|min:1|max:50',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance,out_of_service',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'images' => 'nullable|array|max:15',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'license_plate.unique' => 'The plate number is already registered. Each vehicle must have a unique plate number owned by only 1 user.',
        ]);

        if (empty($validated['user_id']) && empty($vehicle->user_id)) {
            $validated['user_id'] = auth()->id();
        }

        $existingImages = $vehicle->images ?? [];
        if (is_string($existingImages)) {
            $existingImages = json_decode($existingImages, true) ?? [];
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vehicles', 'public');
            $validated['image_path'] = 'storage/'.$path;
            if (! in_array('storage/'.$path, $existingImages)) {
                array_unshift($existingImages, 'storage/'.$path);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('vehicles', 'public');
                $existingImages[] = 'storage/'.$path;
            }
        }

        if (! empty($existingImages)) {
            $validated['images'] = array_values(array_unique(array_filter($existingImages)));
            if (empty($validated['image_path']) && count($validated['images']) > 0) {
                $validated['image_path'] = $validated['images'][0];
            }
        }

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', "Vehicle {$vehicle->name} updated successfully!");
    }

    public function deletePhoto(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        $targetPhoto = $request->image_path;

        $images = $vehicle->images ?? [];
        if (is_string($images)) {
            $images = json_decode($images, true) ?? [];
        }
        $images = array_values(array_filter($images));

        $newImages = array_values(array_filter($images, function ($img) use ($targetPhoto) {
            return $img !== $targetPhoto && ltrim($img, '/') !== ltrim($targetPhoto, '/');
        }));

        $isPrimary = ($vehicle->image_path === $targetPhoto || ltrim((string) $vehicle->image_path, '/') === ltrim($targetPhoto, '/'));

        if ($isPrimary) {
            $vehicle->image_path = count($newImages) > 0 ? $newImages[0] : null;
        }

        $vehicle->images = $newImages;
        $vehicle->save();

        $cleanPath = ltrim($targetPhoto, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $relative = substr($cleanPath, strlen('storage/'));
            Storage::disk('public')->delete($relative);
        }

        return redirect()->back()->with('success', 'Vehicle photo deleted successfully!');
    }

    public function setPrimaryPhoto(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        $targetPhoto = $request->image_path;

        $images = $vehicle->images ?? [];
        if (is_string($images)) {
            $images = json_decode($images, true) ?? [];
        }
        $images = array_values(array_filter($images));

        if (! in_array($targetPhoto, $images)) {
            $images[] = $targetPhoto;
        }

        // Move target photo to front of gallery
        $images = array_values(array_diff($images, [$targetPhoto]));
        array_unshift($images, $targetPhoto);

        $vehicle->image_path = $targetPhoto;
        $vehicle->images = array_values(array_unique($images));
        $vehicle->save();

        return redirect()->back()->with('success', 'Primary cover photo updated successfully!');
    }

    public function toggleAvailability(Request $request, Vehicle $vehicle)
    {
        $isOwner = ($vehicle->user_id && $vehicle->user_id === auth()->id());
        $isAdmin = auth()->user()->isAdmin();
        $isUnassigned = empty($vehicle->user_id);

        if (! $isOwner && ! $isAdmin && ! $isUnassigned) {
            if ($request->header('X-Inertia')) {
                return back()->with('error', 'Unauthorized action.');
            }
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            abort(403);
        }

        if ($isUnassigned && empty($vehicle->user_id)) {
            $vehicle->user_id = auth()->id();
        }

        if ($request->has('status')) {
            $inputStatus = strtolower(trim((string) $request->status));
            if ($inputStatus === 'available' || $inputStatus === 'on' || $inputStatus === '1' || $inputStatus === 'true') {
                $newStatus = 'available';
            } else {
                $newStatus = 'out_of_service';
            }
        } else {
            $newStatus = ($vehicle->status === 'available') ? 'out_of_service' : 'available';
        }

        $vehicle->status = $newStatus;
        $vehicle->save();

        $message = "{$vehicle->name} is now ".($vehicle->status === 'available' ? 'Available' : 'Turned Off').'.';

        if (! $request->header('X-Inertia') && ($request->wantsJson() || $request->ajax())) {
            return response()->json([
                'success' => true,
                'status' => $vehicle->status,
                'is_available' => ($vehicle->status === 'available'),
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
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
