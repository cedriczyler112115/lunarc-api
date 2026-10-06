<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_code',
        'vehicle_id',
        'user_id',
        'destination_id',
        'destination',
        'destination_rate',
        'reservation_fee',
        'customer_name',
        'customer_email',
        'customer_phone',
        'driver_license_path',
        'pickup_location',
        'pickup_time',
        'return_time',
        'start_date',
        'end_date',
        'total_days',
        'daily_rate',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'daily_rate' => 'decimal:2',
        'destination_rate' => 'decimal:2',
        'reservation_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destinationModel(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public static function generateBookingCode(): string
    {
        $prefix = 'LNR-'.date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));

        return $prefix.'-'.$random;
    }
}
