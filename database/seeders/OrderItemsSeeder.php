<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemsSeeder extends Seeder
{
    public function run(): void
    {
        OrderItem::factory(10)->create();
    }
}
