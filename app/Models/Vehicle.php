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
     * Check if the vehicle is available for a given date and time range (with 2-hour carwash buffer).
     */
    public function isAvailableForDates($startDate, $endDate, $excludeBookingId = null, $pickupTime = null, $returnTime = null): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        $pTime = !empty($pickupTime) ? $pickupTime : '00:00';
        $rTime = !empty($returnTime) ? $returnTime : '23:59';

        $reqStart = \Carbon\Carbon::parse($startDate . ' ' . $pTime);
        $reqEnd = \Carbon\Carbon::parse($endDate . ' ' . $rTime);
        $reqEndWithBuffer = $reqEnd->copy()->addHours(2);

        $existingBookings = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($excludeBookingId, function ($q) use ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            })
            ->get();

        foreach ($existingBookings as $booking) {
            $bStartStr = $booking->start_date->format('Y-m-d') . ' ' . ($booking->pickup_time ?: '00:00');
            $bEndStr = $booking->end_date->format('Y-m-d') . ' ' . ($booking->return_time ?: '23:59');

            $bStart = \Carbon\Carbon::parse($bStartStr);
            $bEnd = \Carbon\Carbon::parse($bEndStr);
            $bEndWithBuffer = $bEnd->copy()->addHours(2);

            // Interval overlap check with 2-hour carwash buffer
            if ($reqStart->lt($bEndWithBuffer) && $reqEndWithBuffer->gt($bStart)) {
                return false;
            }
        }

        return true;
    }
}

