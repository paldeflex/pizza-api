<?php

namespace Database\Seeders;

use App\Models\CartItem;
use Illuminate\Database\Seeder;

class CartItemsSeeder extends Seeder
{
    public function run(): void
    {
        CartItem::factory(20)->create();
    }
}
