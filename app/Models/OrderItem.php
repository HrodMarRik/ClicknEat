<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'dish_id',
        'quantity',
        'price',
        'notes',
    ];

    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer',
    ];

    /**
     * Relation avec la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation avec le plat
     */
    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

    /**
     * Calcule le sous-total de cet élément de commande
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
