<?php

use Faker\Generator as Faker;

use App\Item;
use App\Comment;
use App\User;

$factory->define(Comment::class, function (Faker $faker) {

	$ids = Item::pluck('id');
    $userIDs = User::pluck('id');

	$id = $faker->randomElement($ids->toArray());
	$userID = $faker->randomElement($userIDs->toArray());
 
    return [
        "item_id" => $id,
        "user_id" => $userID,
        "comment" => $faker->text,
    ];
});
