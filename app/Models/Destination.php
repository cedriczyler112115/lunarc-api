<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'region',
        'province',
        'city',
        'destination_rate',
        'description',
    ];

    protected $casts = [
        'destination_rate' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function vehicleRates(): HasMany
    {
        return $this->hasMany(DestinationVehicleRate::class);
    }

    /**
     * Get the destination rate for a specific vehicle type ID or VehicleType model.
     * Fallbacks to baseline destination_rate if not explicitly set.
     */
    public function getRateForVehicleType($vehicleTypeIdOrType = null): float
    {
        if (! $vehicleTypeIdOrType) {
            return (float) $this->destination_rate;
        }

        $typeId = $vehicleTypeIdOrType instanceof VehicleType ? $vehicleTypeIdOrType->id : $vehicleTypeIdOrType;

        $specificRate = $this->relationLoaded('vehicleRates')
            ? $this->vehicleRates->firstWhere('vehicle_type_id', $typeId)
            : $this->vehicleRates()->where('vehicle_type_id', $typeId)->first();

        if ($specificRate !== null) {
            return (float) $specificRate->destination_rate;
        }

        return (float) $this->destination_rate;
    }
}
