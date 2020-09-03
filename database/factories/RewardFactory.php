<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

$factory->define(App\Reward::class, function (Faker $faker) {

    $values = [5, 10, 15, 20, 25, 50];
    $complaintIds = DB::table('complaints')->pluck('id')->toArray();

    return [
        'reward_provider_id' => App\RewardProvider::inRandomOrder()->first()->id,
        'value' => $values[$faker->numberBetween(0, 5)],
        'code' => $faker->unique()->ean13,
        //'complaint_id' => $faker->unique()->randomElement($complaintIds),
        'complaint_id' => function() use ($faker, $complaintIds) {
            return rand(0,1) === 0 ? null : $faker->unique()->randomElement($complaintIds);
        },
    ];
});