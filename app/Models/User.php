<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'is_approved',
    'is_admin',
    'role',
    'first_name',
    'last_name',
    'middle_name',
    'extension_name',
    'birthday',
    'address',
    'contact_number',
    'avatar_path',
    'owner_description',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'is_admin' => 'boolean',
            'birthday' => 'date',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    public function isCarOwner(): bool
    {
        return $this->role === 'car_owner';
    }

    public function isGuest(): bool
    {
        return $this->role === 'guest' || empty($this->role);
    }

    public function getFormattedNameAttribute(): string
    {
        if ($this->first_name || $this->last_name) {
            $parts = array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
                $this->extension_name,
            ]);

            return implode(' ', $parts);
        }

        return $this->name ?? 'User';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! empty($this->avatar_path)) {
            return asset($this->avatar_path);
        }

        return null;
    }
}
