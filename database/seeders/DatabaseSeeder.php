<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            ProductsSeeder::class,
            CartItemsSeeder::class,
            OrdersSeeder::class,
            OrderItemsSeeder::class,
        ]);
    }
}
