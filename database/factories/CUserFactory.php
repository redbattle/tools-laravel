<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\CUser::class, function (Faker $faker) {
    return [
        'nickname' => $faker->name,
        'avatar' => $faker->imageUrl(),
        'password' => bcrypt(md5('123456')),
        'status' => $faker->numberBetween(0, 1),
    ];
});

$factory->define(\App\Models\CAccount::class, function (Faker $faker) {
    return [
        'mode' => 'phone',
        'username' => $faker->numerify('13#########'),
        'uid' => function () {
            return factory(\App\Models\CUser::class)->create()->id;
        }
    ];
});
