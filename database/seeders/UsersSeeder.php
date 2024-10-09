<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'paldeflex@gmail.com',
            'password' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'pavel',
            'email' => 'pavel228@mail.ru',
            'password' => Hash::make('228322'),
        ]);

        User::factory(10)->create();
    }
}
