<?php

namespace App\Dto;

readonly class CartItemDto
{
    public function __construct(
        public int $user_id,
        public int $product_id,
        public int $quantity
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
        ];
    }
}
