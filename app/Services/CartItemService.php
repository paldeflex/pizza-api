<?php

namespace App\Services;

use App\DTO\CartItemDto;
use App\Enums\ProductType;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class CartItemService
{
    public function findCartItem(int $userId, int $productId): ?CartItem
    {
        return CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function createCartItem(CartItemDto $dto): void
    {
        CartItem::create($dto->toArray());
    }

    public function updateCartItemQuantity(CartItem $cartItem, int $quantity): void
    {
        $cartItem->quantity = $quantity;
        $cartItem->save();
    }

    public function calculateTotalPrice(int $userId): int
    {
        $cartItems = $this->getUserCartItems($userId);

        return $cartItems->sum(fn ($cartItem) => $cartItem->product->price * $cartItem->quantity);
    }

    public function getUserCartItems(int $userId): Collection
    {
        return CartItem::where('user_id', $userId)
            ->with('product')
            ->get();
    }

    public function checkProductLimits(int $userId, Product $product): ?array
    {
        $limits = [
            ProductType::PIZZA->value => 10,
            ProductType::DRINK->value => 20,
        ];

        $limit = $limits[$product->type->value] ?? null;

        if ($limit) {
            $totalCount = $this->getTotalProductCount($userId, $product->type->value);

            if ($totalCount >= $limit) {
                return ['message' => "You cannot add more than {$limit} {$product->type->value}s to the cart"];
            }
        }

        return null;
    }

    public function getTotalProductCount(int $userId, string $type): int
    {
        return CartItem::where('user_id', $userId)
            ->whereHas('product', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->sum('quantity');
    }
}
