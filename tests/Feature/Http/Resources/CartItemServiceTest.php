<?php

namespace Tests\Feature\Http\Resources;

use App\DTO\CartItemDto;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CartItemService $cartItemService;

    protected User $user;

    public function test_find_cart_item_returns_correct_item()
    {
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);

        $foundCartItem = $this->cartItemService->findCartItem($this->user->id, $product->id);

        $this->assertNotNull($foundCartItem);
        $this->assertEquals($cartItem->id, $foundCartItem->id);
    }

    public function test_find_cart_item_returns_null_if_not_found()
    {
        $product = Product::factory()->create();

        $cartItem = $this->cartItemService->findCartItem($this->user->id, $product->id);

        $this->assertNull($cartItem);
    }

    public function test_create_cart_item_creates_new_item()
    {
        $product = Product::factory()->create();
        $cartItemDto = new CartItemDto($this->user->id, $product->id, 2);

        $this->cartItemService->createCartItem($cartItemDto);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_update_cart_item_quantity_updates_quantity()
    {
        $cartItem = CartItem::factory()->create([
            'user_id' => $this->user->id,
            'quantity' => 1,
        ]);

        $this->cartItemService->updateCartItemQuantity($cartItem, 5);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_calculate_total_price_returns_correct_sum()
    {
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 200]);

        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product1->id,
            'quantity' => 1,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);

        $totalPrice = $this->cartItemService->calculateTotalPrice($this->user->id);

        $this->assertEquals(500, $totalPrice);
    }

    public function test_get_user_cart_items_returns_correct_items()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product1->id,
        ]);

        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product2->id,
        ]);

        $cartItems = $this->cartItemService->getUserCartItems($this->user->id);

        $this->assertCount(2, $cartItems);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartItemService = new CartItemService;
        $this->user = User::factory()->create();
    }
}
