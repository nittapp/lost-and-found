<?php


use Faker\Generator as Faker;
use App\AuthorizationLevel;
use App\User;


$factory->define(User::class, function (Faker $faker) {
    
    $authorizationLevelIDs = AuthorizationLevel::pluck('id');

    return [
        'username' => str_random(10),
        'name' => $faker->name,
        'auth_user_id' => $faker->randomElement($authorizationLevelIDs->toArray()),
    ];
});
