<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    /** @test */
    public function it_lists_all_products()
    {
        Product::factory(5)->create();

        $response = $this->getJson('/api/products');

        $this->assertEquals(
            $response->json(),
            Product::with('inventory')->get()->toArray()
        );
    }

    /** @test */
    public function it_lists_a_single_product()
    {
        Product::factory(5)->create();

        $response = $this->getJson('/api/products/2');

        $this->assertEquals(
            $response->json(),
            Product::with('inventory')->find(2)->toArray()
        );
    }

    /** @test */
    public function it_stores_a_new_product()
    {
        $product = Product::factory()->make(['count' => 10]);

        $this->postJson('/api/products', $product->toArray());

        $this->assertDatabaseCount('products', 1);
        $this->assertEquals(Product::first()->inventory->count, 10);
    }

    /** @test */
    public function it_updates_an_existing_product()
    {
        $product = Product::factory()->create();

        $this->putJson("/api/products/{$product->id}", [
            'price' => 2499,
            'count' => 4,
        ]);

        $product->refresh();

        $this->assertEquals($product->price, 2499);
        $this->assertEquals($product->inventory->count, 4);
    }

    /** @test */
    public function it_deletes_an_existing_product()
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}");

        $this->assertDatabaseCount('inventories', 0);
        $this->assertDatabaseCount('products', 0);
    }
}
