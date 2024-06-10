<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_create_product()
    {
        $seller = User::factory()->create(['role' => 'seller']);

        $response = $this->actingAs($seller, 'sanctum')->postJson('/api/products', [
            'name' => 'Product 1',
            'description' => 'Description of Product 1',
            'price' => 100.00,
            'quantity' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'description', 'price', 'quantity']);
    }

    public function test_seller_can_update_product()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['user_id' => $seller->id]);

        $response = $this->actingAs($seller, 'sanctum')->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 150.00,
            'quantity' => 20,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => 'Updated Product',
                'description' => 'Updated Description',
                'price' => 150.00,
                'quantity' => 20,
            ]);
    }

    public function test_seller_can_delete_product()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['user_id' => $seller->id]);

        $response = $this->actingAs($seller, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_only_owner_can_update_product()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($buyer, 'sanctum')->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 150.00,
            'quantity' => 20,
        ]);

        $response->assertStatus(401);
    }

    public function test_only_owner_can_delete_product()
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['user_id' => $seller->id]);

        $buyer = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($buyer, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(401);
    }


    public function test_buyer_can_view_products()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        Product::factory(3)->create(['user_id' => $buyer->id]);

        $response = $this->actingAs($buyer, 'sanctum')->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active',
                    ],
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_buyer_cannot_create_product()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($buyer, 'sanctum')->postJson('/api/products', [
            'name' => 'Product 1',
            'description' => 'Description of Product 1',
            'price' => 100.00,
            'quantity' => 10,
        ]);

        $response->assertStatus(403); // Forbidden
    }
}