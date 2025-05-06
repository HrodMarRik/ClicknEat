<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Dish;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $dish = Dish::factory()->create();
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $dish->price;

        return [
            'cart_id' => Cart::factory(),
            'dish_id' => $dish->id,
            'quantity' => $quantity,
            'price' => $price,
        ];
    }
}
