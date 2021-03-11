<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    /** @test */
    public function it_lists_all_orders_for_a_user()
    {
        $user = User::factory()->create();

        Order::factory(2)
            ->hasItems(2)
            ->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders?user_id=1');

        $this->assertCount(2, $response->json());

        $this->assertEquals(
            $response->json(),
            Order::whereUserId(1)->get()->toArray()
        );
    }

    /** @test */
    public function it_does_not_list_an_order_for_an_invalid_user()
    {
        $user = User::factory()->create();

        Order::factory(2)
            ->hasItems(2)
            ->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders/1?user_id=10');
        $response->assertStatus(404);
    }

    /** @test */
    public function it_lists_an_order_for_a_user()
    {
        $user = User::factory()->create();

        Order::factory(2)
            ->hasitems(2)
            ->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders/1?user_id=1');

        $this->assertEquals(
            $response->json(),
            Order::whereUserId(1)->first()->toArray()
        );
    }

    /** @test */
    public function it_rejects_a_new_order_if_a_product_inventory_isnt_enough()
    {
        $products = Product::factory(4)->create();
        $order = Order::factory()->make([
            'items' => [
                ['product_id' => 1, 'quantity' => 1],
                ['product_id' => 2, 'quantity' => 4],
            ]
        ]);

        $response = $this->postJson('/api/orders', $order->toArray());
        $response->assertStatus(403);

        $this->assertEquals($products[0]->inventory->count, 2);
        $this->assertEquals($products[1]->inventory->count, 2);
    }

    /** @test */
    public function it_stores_a_new_order()
    {
        $this->withoutExceptionHandling();

        $products = Product::factory(2)->create();
        $order = Order::factory()->make([
            'items' => $products->map(function ($p) {
                return ['product_id' => $p->id, 'quantity' => 1];
            })->toArray()
        ]);

        $this->postJson('/api/orders', $order->toArray());

        $this->assertEquals($products[0]->inventory->count, 1);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 2);
    }

    /** @test */
    public function it_does_not_update_an_order_for_an_invalid_user()
    {
        $order = Order::factory()->hasItems(2)->create();
        $orderItem = OrderItem::factory()->make(['order_id' => $order->id]);

        $response = $this->putJson("/api/orders/{$order->id}?user_id=10", [
            'items' => $order->items->add($orderItem)->toArray()
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_updates_an_existing_order()
    {
        $order = Order::factory()->hasItems(2)->create();
        $orderItems = OrderItem::factory(2)->make(['order_id' => $order->id]);

        $this->putJson("/api/orders/{$order->id}?user_id=1", [
            'items' => $order->items->concat($orderItems->toArray())->toArray()
        ]);

        $this->assertDatabaseCount('order_items', 4);
    }

    /** @test */
    public function it_does_not_delete_an_order_for_an_invalid_user()
    {
        Order::factory()->hasItems(2)->create();

        $response = $this->deleteJson('/api/orders/1?user_id=10');
        $response->assertStatus(404);
    }

    /** @test */
    public function it_deletes_an_existing_order()
    {
        $order = Order::factory()->hasItems(2)->create();

        $this->deleteJson("/api/orders/{$order->id}?user_id=1");

        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('orders', 0);
    }
}
