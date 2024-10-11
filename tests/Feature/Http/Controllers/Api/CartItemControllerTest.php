<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Enums\ProductType;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function test_store_requires_authentication()
    {
        $response = $this->postJson('/api/cart/add', []);

        $response->assertStatus(401);
    }

    public function test_store_fails_if_product_does_not_exist()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/cart/add', [
            'product_id' => 999,
        ]);

        $response->assertStatus(422);
    }

    public function test_store_adds_new_item_to_cart()
    {
        $this->actingAs($this->user, 'api');
        $product = Product::factory()->create();

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_store_increments_quantity_if_item_already_in_cart()
    {
        $this->actingAs($this->user, 'api');
        $product = Product::factory()->create();
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(401);
    }

    public function test_index_returns_cart_items_with_total_price()
    {
        $this->actingAs($this->user, 'api');
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

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'total_price' => 500,
            ],
        ]);
    }

    public function test_destroy_requires_authentication()
    {
        $response = $this->deleteJson('/api/cart/remove/1');

        $response->assertStatus(401);
    }

    public function test_destroy_removes_item_from_cart()
    {
        $this->actingAs($this->user, 'api');
        $cartItem = CartItem::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/cart/remove/{$cartItem->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_destroy_returns_error_if_item_not_found()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->deleteJson('/api/cart/remove/999');

        $response->assertStatus(404);
    }

    public function test_cannot_add_more_than_10_pizzas_to_cart()
    {
        $this->actingAs($this->user, 'api');
        $pizza = Product::factory()->create([
            'type' => ProductType::PIZZA->value,
        ]);

        CartItem::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'product_id' => $pizza->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $pizza->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'You cannot add more than 10 pizzas to the cart']);
    }

    public function test_cannot_add_more_than_20_drinks_to_cart()
    {
        $this->actingAs($this->user, 'api');
        $drink = Product::factory()->create([
            'type' => ProductType::DRINK->value,
        ]);

        CartItem::factory()->count(20)->create([
            'user_id' => $this->user->id,
            'product_id' => $drink->id,
            'quantity' => 1,
        ]);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $drink->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'You cannot add more than 20 drinks to the cart']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
}
