<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationVehicleRate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'destination_id',
        'vehicle_type_id',
        'destination_rate',
    ];

    protected $casts = [
        'destination_rate' => 'decimal:2',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }
}
