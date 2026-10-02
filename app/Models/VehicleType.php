<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VehicleType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($type) {
            if (empty($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function destinationRates(): HasMany
    {
        return $this->hasMany(DestinationVehicleRate::class);
    }
}
