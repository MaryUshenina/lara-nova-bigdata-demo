<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Ad;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$user =  User::first();
$countries = array_flip(\Countries::getList('en'));

$factory->define(Ad::class, function (Faker $faker) use ($user, $countries) {
    return [
        'user_id' => $user->id ?? 1,
        'title' => $faker->sentence,
        'description' => $faker->text(1000),
        'phone' => '+1 ' . $faker->numerify('(###) ###-####'),
        'country' => $faker->randomElement($countries),
        'price' => $faker->randomFloat(2, 0, 99999.99),
        'email' =>$faker->email,
        'end_date' =>$faker->dateTimeBetween('-1 month', '+3 months'),
        'created_at'=> $faker->dateTimeBetween('-3 month')
    ];
});
