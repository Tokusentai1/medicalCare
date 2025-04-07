<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'cart_id',
        'status',
        'medicines',
        'quantities',
        'total_price'
    ];

    protected $hidden = [
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'medicines' => 'array',
            'quantities' => 'array',
        ];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }


    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status']);
    }
}
