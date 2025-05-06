<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'description',
        'price',
        'category',
        'image',
        'available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function restaurant()
    {
        return $this->hasOneThrough(Restaurant::class, Menu::class, 'id', 'id', 'menu_id', 'restaurant_id');
    }
}
