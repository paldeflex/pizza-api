<?php

namespace App\Http\Controllers\Api;

use App\Dto\CartItemDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Services\CartItemService;
use App\Services\ProductService;

class CartItemController extends Controller
{
    protected CartItemService $cartItemService;

    protected ProductService $productService;

    public function __construct(CartItemService $cartService, ProductService $productService)
    {
        $this->cartItemService = $cartService;
        $this->productService = $productService;
    }

    public function store(AddToCartItemRequest $request)
    {
        $productId = $request->validated('product_id');
        $userId = auth()->id();

        $product = $this->productService->getProductById($productId);

        $limitCheck = $this->cartItemService->checkProductLimits($userId, $product);
        if ($limitCheck) {
            return response()->json($limitCheck, 400);
        }

        $cartItem = $this->cartItemService->findCartItem($userId, $productId);

        if ($cartItem) {
            $this->cartItemService->updateCartItemQuantity($cartItem, $cartItem->quantity + 1);
        } else {
            $cartItemDto = new CartItemDto($userId, $productId, 1);
            $this->cartItemService->createCartItem($cartItemDto);
        }

        return response()->json(['message' => 'Item successfully added to cart']);
    }

    public function index()
    {
        $cartItems = $this->cartItemService->getUserCartItems(auth()->id());
        $totalPrice = $this->cartItemService->calculateTotalPrice(auth()->id());

        return response()->json([
            'data' => [
                'items' => CartItemResource::collection($cartItems),
                'total_price' => $totalPrice,
            ],
        ]);
    }

    public function destroy(CartItem $cartItem)
    {
        return $cartItem->delete()
            ? response()->json(['message' => 'Item removed from cart'])
            : response()->json(['message' => 'Failed to remove item from cart'], 500);
    }
}
