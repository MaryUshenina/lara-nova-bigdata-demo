<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {

    $categories = Category::all()->pluck('id');
     return [
         'pid' => count($categories) ? $faker->randomElement($categories) : null,
         'name' => $faker->text(20),
     ];

});
