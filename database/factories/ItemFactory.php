<?php

use Faker\Generator as Faker;
use App\Item;
use App\User;
     
$factory->define(Item::class, function (Faker $faker) {

	$userIDs = User::pluck('id');

    return [
        "user_id" => $faker->randomElement($userIDs->toArray()),
        "title" => $faker->sentence,
        "description" => $faker->paragraph,
        "created_at" => $faker->dateTimeBetween('2017-10-10 00:00:00', '2017-10-23 00:00:00'),
    ];
});
