<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Authenticatable implements FilamentUser, HasName
{
    use LogsActivity, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'gender',
        'birth_date',
        'shift',
        'day',
        'role'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'password' => 'hashed',
            'day' => 'array',
            'role' => 'array',
        ];
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@admin.com');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setFirstNameAttribute($value): void
    {
        $this->attributes['first_name'] = ucfirst($value);
    }

    public function setLastNameAttribute($value): void
    {
        $this->attributes['last_name'] = ucfirst($value);
    }

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'password', 'phone_number', 'gender', 'birth_date', 'shift', 'day', 'role']);
    }
}
