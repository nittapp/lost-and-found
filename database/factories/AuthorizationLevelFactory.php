<?php

use Faker\Generator as Faker;
use App\AuthorizationLevel;

$factory->define(AuthorizationLevel::class, function (Faker $faker) {
    return [
       "type" => "student",
       "create" => true,
       "edit" => true,
       "delete" => true,
    ];
});
