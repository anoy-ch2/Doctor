<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => '$2y$10$YlmGnjqSgaBDskX0IZ0LXe7RhbXtO1g0.BgHKU6MiPFqTXfjpG9du',
                'remember_token' => null,
            ],
        ];

        User::insert($users);
    }
}
