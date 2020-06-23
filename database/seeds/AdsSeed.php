<?php

use Illuminate\Database\Seeder;
use \App\Models\Ad;
use \App\Models\User;
use \Illuminate\Support\Facades\Hash;

class AdsSeed extends Seeder
{
    const ADMIN_EMAIL = 'admin@testapp.com';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::firstOrCreate([
            'email' => self::ADMIN_EMAIL,
            'role'=> User::ADMIN_USER_ROLE
        ], [
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);


        for ($i = 0; $i <= 20; $i++) {
            factory(Ad::class)->create();
        }
    }
}
