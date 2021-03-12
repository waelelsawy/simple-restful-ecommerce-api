<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    /** @test */
    public function it_lists_all_orders_for_a_user()
    {
        Sanctum::actingAs($user = User::factory()->create());

        Order::factory(2)
            ->hasItems(2)
            ->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders');

        $this->assertCount(2, $response->json());

        $this->assertEquals(
            $response->json(),
            Order::whereUserId(1)->get()->toArray()
        );
    }

    /** @test */
    public function it_does_not_list_an_order_for_an_invalid_user()
    {
        Sanctum::actingAs(User::factory()->create());

        Order::factory(2)->hasItems(2)->create();

        $response = $this->getJson('/api/orders/1');
        $response->assertStatus(403);
    }

    /** @test */
    public function it_lists_an_order_for_a_user()
    {
        Sanctum::actingAs($user = User::factory()->create());

        Order::factory(2)
            ->hasitems(2)
            ->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders/1');

        $this->assertEquals(
            $response->json(),
            Order::whereUserId(1)->first()->toArray()
        );
    }

    /** @test */
    public function it_rejects_a_new_order_if_an_inventory_isnt_enough()
    {
        Sanctum::actingAs(User::factory()->create());

        $products = Product::factory(4)->create();
        $order = Order::factory()->make([
            'items' => [
                ['product_id' => 1, 'quantity' => 1],
                ['product_id' => 2, 'quantity' => 4],
            ]
        ]);

        $response = $this->postJson('/api/orders', $order->toArray());
        $response->assertStatus(422);

        $this->assertEquals($products[0]->inventory->count, 2);
        $this->assertEquals($products[1]->inventory->count, 2);
    }

    /** @test */
    public function it_stores_a_new_order()
    {
        Sanctum::actingAs(User::factory()->create());

        $products = Product::factory(2)->create();
        $order = Order::factory()->make([
            'items' => [
                ['product_id' => 1, 'quantity' => 1],
                ['product_id' => 2, 'quantity' => 1],
            ]
        ]);

        $response = $this->postJson('/api/orders', $order->toArray());

        $this->assertEquals($response->json(), Order::first()->toArray());
        $this->assertEquals($products[0]->inventory->count, 1);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 2);
    }

    /** @test */
    public function it_does_not_update_an_order_for_an_invalid_user()
    {
        Sanctum::actingAs(User::factory()->create());

        $order = Order::factory()->hasItems(2)->create();
        $orderItem = OrderItem::factory()->make(['order_id' => $order->id]);

        $response = $this->putJson('/api/orders/1', [
            'items' => $order->items->add($orderItem)->toArray()
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_updates_an_existing_order()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $order = Order::factory()
            ->hasItems(2)
            ->create(['user_id' => $user->id]);

        $orderItems = OrderItem::factory(2)->make(['order_id' => $order->id]);

        $response = $this->putJson('/api/orders/1', [
            'items' => $order->items->concat($orderItems->toArray())->toArray()
        ]);

        $this->assertEquals($response->json(), Order::first()->toArray());
        $this->assertDatabaseCount('order_items', 4);
    }

    /** @test */
    public function it_does_not_delete_an_order_for_an_invalid_user()
    {
        Sanctum::actingAs(User::factory()->create());

        Order::factory()->hasItems(2)->create();

        $response = $this->deleteJson('/api/orders/1');
        $response->assertStatus(403);
    }

    /** @test */
    public function it_deletes_an_existing_order()
    {
        Sanctum::actingAs($user = User::factory()->create());

        Order::factory()
            ->hasItems(2)
            ->create(['user_id' => $user->id]);

        $this->deleteJson('/api/orders/1');

        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('orders', 0);
    }
}
