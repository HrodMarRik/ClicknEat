<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $dish = Dish::factory()->create();
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $dish->price;
        $subtotal = $price * $quantity;

        return [
            'order_id' => Order::factory(),
            'dish_id' => $dish->id,
            'dish_name' => $dish->name,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal,
        ];
    }
}
