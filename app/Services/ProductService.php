<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getProductById(int $productId): Product
    {
        return Product::findOrFail($productId);
    }
}
