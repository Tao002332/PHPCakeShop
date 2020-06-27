<?php

use Faker\Generator as Faker;


$factory->define(\App\Model\Admin::class, function (Faker $faker) {
    static $password;
    return [
        'admin_name' => $faker->name,
        'password' => $password?:$password=bcrypt("admin123"), //secret
        'data_flag' => 1,
    ];
});




