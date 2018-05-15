<?php

use Faker\Generator as Faker;

$factory->define(App\Complaint::class, function (Faker $faker) {

    return [
        'user_id' => App\User::inRandomOrder()->first()->id,
        'customer_id' => App\Customer::inRandomOrder()->first()->id,
        'description' => $faker->paragraph(4, true),
        'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
    ];
});
