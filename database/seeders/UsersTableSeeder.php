<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usernames = ['user1', 'user2', 'user3', 'user4'];
        $password = app('hash')->make('password');

        collect($usernames)->each(function (string $username) use ($password) {
            User::create([
                'username' => $username,
                'password' => $password
            ]);
        });
    }
}
