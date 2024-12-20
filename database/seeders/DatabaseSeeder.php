<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name'     => 'Ronald Besinga',
            'email'    => 'admin@gmail.com',
            'password' => bcrypt("123"),
            'role'     => 'owner',
            'isActive' => 'active'
        ]);


        User::factory()->create([
            'name'     => 'Ben Ten',
            'email'    => 'ben@gmail.com',
            'password' => bcrypt("123"),
            'role'     => 'user',
            'isActive' => 'active'
        ]);
    }
}
