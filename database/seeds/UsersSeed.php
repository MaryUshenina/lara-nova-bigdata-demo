<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() < 1000) {
            factory(User::class, 700)->create([
                'role' => User::PLAIN_USER_ROLE,
            ]);

            factory(User::class, 300)->create([
                'role' => User::ESTATE_USER_ROLE,
            ]);
        }
    }
}
