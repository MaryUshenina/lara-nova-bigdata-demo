<?php

/** @var Factory $factory */

use App\Models\Ad;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

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

$user = User::first();
$countries = array_flip(Countries::getList(config('app.locale')));
$users = User::whereIn('role', User::ROLES_WITH_ADS)->pluck('id');

$factory->define(Ad::class, function (Faker $faker) use ($user, $countries, $users) {
    return [
        'user_id' => $faker->randomElement($users),
        'title' => $faker->sentence,
        'description' => $faker->text(1000),
        'phone' => '+1 '.$faker->numerify('(###) ###-####'),
        'country' => $faker->randomElement($countries),
        'price' => $faker->randomFloat(2, 0, 99999.99),
        'email' => $faker->email,
        'end_date' => $faker->dateTimeBetween('-1 month', '+3 months'),
        'created_at_date' => $faker->dateTimeBetween('-3 month')
    ];
});
