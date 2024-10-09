<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(0, 100),
            'price' => fake()->numberBetween(0, 1000),
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
