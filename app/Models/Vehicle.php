<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, HasUuids;


    protected $fillable = [
        'user_id',
        'vehicle_type_id',
        'name',
        'make',
        'model',
        'year',
        'license_plate',
        'color',
        'transmission',
        'fuel_type',
        'seats',
        'daily_rate',
        'status',
        'image_path',
        'description',
    ];

    protected $casts = [
        'year' => 'integer',
        'seats' => 'integer',
        'daily_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if the vehicle is available for a given date range.
     */
    public function isAvailableForDates($startDate, $endDate, $excludeBookingId = null): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        $query = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }
}

