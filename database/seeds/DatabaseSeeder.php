<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\AuthorizationLevel::class, 1)->create();
        // factory(App\User::class, 3)->create();
        // factory(App\Item::class, 20)->create();
        // factory(App\Comment::class, 25)->create();
    }
}
