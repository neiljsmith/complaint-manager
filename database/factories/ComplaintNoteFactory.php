<?php

use Faker\Generator as Faker;

$factory->define(App\ComplaintNote::class, function (Faker $faker) {
    return [
        'user_id' => App\User::inRandomOrder()->first()->id,
        'complaint_id' => App\Complaint::inRandomOrder()->first()->id,
        'content' => $faker->paragraph(3, true),
    ];
});
