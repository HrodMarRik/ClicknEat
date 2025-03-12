<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $statuses = ['pending', 'preparing', 'ready', 'delivering', 'delivered', 'cancelled'];
        $subtotal = $this->faker->randomFloat(2, 20, 100);
        $deliveryFee = $this->faker->randomFloat(2, 0, 5);
        $total = $subtotal + $deliveryFee;

        return [
            'user_id' => User::factory()->client(),
            'restaurant_id' => Restaurant::factory(),
            'status' => $this->faker->randomElement($statuses),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->optional()->sentence(),
            'payment_method' => $this->faker->randomElement(['card', 'cash']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
        ];
    }

    public function pending(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function delivered(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
                'payment_status' => 'paid',
            ];
        });
    }
}
