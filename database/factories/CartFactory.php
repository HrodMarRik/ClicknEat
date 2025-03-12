<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 20, 100);
        $deliveryFee = $this->faker->randomFloat(2, 0, 5);
        $total = $subtotal + $deliveryFee;

        return [
            'user_id' => User::factory()->client(),
            'restaurant_id' => Restaurant::factory(),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
        ];
    }
}
