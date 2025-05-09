<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MedicalHistory extends Model
{
    use LogsActivity;

    protected $fillable = [
        'allergies',
        'previous_surgeries',
        'past_medical_condition',
        'medications',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'allergies' => 'array',
            'previous_surgeries' => 'array',
            'past_medical_condition' => 'array',
            'medications' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['allergies', 'previous_surgeries', 'past_medical_condition', 'medications']);
    }
}
