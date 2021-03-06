<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /** @test */
    public function it_lists_all_users()
    {
        $users = User::factory(5)->create();

        $response = $this->getJson('/api/users');

        $this->assertEquals($response->json(), $users->toArray());
    }

    /** @test */
    public function it_lists_a_single_user()
    {
        User::factory(5)->create();

        $response = $this->getJson('/api/users/2');

        $this->assertEquals($response->json(), User::find(2)->toArray());
    }

    /** @test */
    public function it_stores_a_new_user()
    {
        $user = User::factory()->make();
        $payload = $user->toArray() + [
            'password' => '1234',
            'password_confirmation' => '1234'
        ];

        $this->postJson('/api/users', $payload);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    /** @test */
    public function it_updates_an_existing_user()
    {
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'New Name'
        ]);

        $this->assertSame($response->json()['name'], 'New Name');
    }

    /** @test */
    public function it_deletes_an_existing_user()
    {
        $user = User::factory()->create();

        $this->deleteJson("/api/users/{$user->id}");

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
