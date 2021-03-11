<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
            'name' => 'Wael',
            'email' => 'wael@email.com',
            'password' => bcrypt('1234')
        ]);

        User::factory(2)->create();

        Product::factory(2)->create();

        Order::factory(5)->hasItems(3)->create(['user_id' => $user->id]);
    }
}
