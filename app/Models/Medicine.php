<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Medicine extends Model
{
    use LogsActivity;

    protected $fillable = [
        'brand_name',
        'composition',
        'dosage',
        'dosage_form',
        'image',
        'quantity',
        'price',
        'manufacture_date',
        'expire_date',
        'manufacturer',
        'rocheta',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'manufacture_date' => 'date:Y-m-d',
            'expire_date' => 'date:Y-m-d',
            'rocheta' => 'boolean',
        ];
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_medicine')->withPivot('quantity');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['brand_name', 'composition', 'dosage', 'dosage_form', 'image', 'quantity', 'price', 'manufacture_date', 'expire_date', 'manufacturer', 'rocheta']);
    }
}
