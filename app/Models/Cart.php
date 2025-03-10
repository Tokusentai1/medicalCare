<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'cart_medicine')->withPivot('quantity');
    }
}
