<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Enums\ProductType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    public function test_anyone_can_view_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_admin_can_create_product()
    {
        $token = auth()->login($this->admin);

        $response = $this->postJson('/api/admin/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'type' => ProductType::PIZZA->value,
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Product created successfully',
                'product' => [
                    'name' => 'Test Product',
                    'description' => 'Test Description',
                    'price' => 100,
                    'type' => ProductType::PIZZA->value,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
        ]);
    }

    public function test_non_admin_cannot_create_product()
    {
        $token = auth()->login($this->user);

        $response = $this->postJson('/api/admin/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'type' => ProductType::PIZZA->value,
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_product()
    {
        $token = auth()->login($this->admin);
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'description' => 'Old Description',
            'price' => 50,
            'type' => ProductType::PIZZA->value,
        ]);

        $response = $this->putJson("/api/admin/products/{$product->id}", [
            'name' => 'New Name',
            'description' => 'New Description',
            'price' => 150,
            'type' => ProductType::DRINK->value,
        ], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product updated successfully',
                'product' => [
                    'name' => 'New Name',
                    'description' => 'New Description',
                    'price' => 150,
                    'type' => ProductType::DRINK->value,
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $token = auth()->login($this->admin);
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}", [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_non_admin_cannot_delete_product()
    {
        $token = auth()->login($this->user);
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}", [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_can_show_product_details()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'type' => $product->type,
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }
}
