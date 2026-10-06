<?php

namespace App\Models;

use Carbon\Carbon;
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
        'images',
        'description',
    ];

    protected $casts = [
        'year' => 'integer',
        'seats' => 'integer',
        'daily_rate' => 'decimal:2',
        'images' => 'array',
    ];

    protected $appends = [
        'all_images',
        'is_rented_today',
    ];

    /**
     * Check if vehicle is rented today (has a confirmed booking matching today's date).
     */
    public function getIsRentedTodayAttribute(): bool
    {
        $today = Carbon::today()->format('Y-m-d');

        if ($this->relationLoaded('bookings')) {
            return $this->bookings
                ->whereIn('status', ['confirmed', 'completed'])
                ->contains(function ($booking) use ($today) {
                    $start = is_string($booking->start_date) ? substr($booking->start_date, 0, 10) : $booking->start_date->format('Y-m-d');
                    $end = is_string($booking->end_date) ? substr($booking->end_date, 0, 10) : $booking->end_date->format('Y-m-d');

                    return $today >= $start && $today <= $end;
                });
        }

        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();
    }

    /**
     * Get all image paths for this vehicle (combining primary image_path and gallery images).
     */
    public function getAllImagesAttribute(): array
    {
        $images = $this->images;
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
        if (! is_array($images)) {
            $images = [];
        }

        if (! empty($this->image_path) && ! in_array($this->image_path, $images)) {
            array_unshift($images, $this->image_path);
        }

        return array_values(array_unique(array_filter($images)));
    }

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
     * Find a conflicting booking for a given date and time range (with 2-hour carwash buffer).
     */
    public function getConflictingBooking($startDate, $endDate, $excludeBookingId = null, $pickupTime = null, $returnTime = null): ?Booking
    {
        $pTime = ! empty($pickupTime) ? $pickupTime : '00:00';
        $rTime = ! empty($returnTime) ? $returnTime : '23:59';

        $reqStart = Carbon::parse($startDate.' '.$pTime);
        $reqEnd = Carbon::parse($endDate.' '.$rTime);
        $reqEndWithBuffer = $reqEnd->copy()->addHours(2);

        $existingBookings = $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->when($excludeBookingId, function ($q) use ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            })
            ->get();

        foreach ($existingBookings as $booking) {
            $bStartStr = $booking->start_date->format('Y-m-d').' '.($booking->pickup_time ?: '00:00');
            $bEndStr = $booking->end_date->format('Y-m-d').' '.($booking->return_time ?: '23:59');

            $bStart = Carbon::parse($bStartStr);
            $bEnd = Carbon::parse($bEndStr);
            $bEndWithBuffer = $bEnd->copy()->addHours(2);

            // Interval overlap check with 2-hour carwash buffer
            if ($reqStart->lt($bEndWithBuffer) && $reqEndWithBuffer->gt($bStart)) {
                return $booking;
            }
        }

        return null;
    }

    /**
     * Check if the vehicle is available for a given date and time range (with 2-hour carwash buffer).
     */
    public function isAvailableForDates($startDate, $endDate, $excludeBookingId = null, $pickupTime = null, $returnTime = null): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        return $this->getConflictingBooking($startDate, $endDate, $excludeBookingId, $pickupTime, $returnTime) === null;
    }
}
