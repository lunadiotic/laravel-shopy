<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed'); // Ensure database is seeded
    }

    /**
     * Test authenticated user can view their orders
     *
     * @return  void
     */
    public function test_authenticated_user_can_view_their_orders()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders');

        $response->dump();

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $order->id]);
    }

    /**
     * Test authenticated user can create order
     *
     * @return  void
     */
    public function test_authenticated_user_can_create_order()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->dump();

        $response->assertStatus(201)
            ->assertJsonFragment(['product_id' => $product->id]);
    }

    /**
     * Test authenticated user can view specific order
     *
     * @return  void
     */
    public function test_user_can_view_specific_order()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $order->id]);
    }

    /**
     * Test authenticated user can update their order
     *
     * @return  void
     */
    public function test_user_can_update_their_order()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/orders/{$order->id}", [
            'quantity' => 3,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['quantity' => 3]);
    }

    /**
     * Test authenticated user can delete their order
     *
     * @return  void
     */
    public function test_user_can_delete_their_order()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(204);
    }

    /**
     * Test authenticated user cannot view others order
     *
     * @return  void
     */
    public function test_user_cannot_view_others_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user cannot update others order
     *
     * @return  void
     */
    public function test_user_cannot_update_others_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->putJson("/api/orders/{$order->id}", [
            'quantity' => 3,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user cannot delete others order
     *
     * @return  void
     */
    public function test_user_cannot_delete_others_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(401);
    }
}