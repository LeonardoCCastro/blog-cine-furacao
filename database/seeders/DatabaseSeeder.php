<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@blog.test',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_writer' => false,
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'writer@blog.test',
        ], [
            'name' => 'Writer',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_writer' => true,
            'email_verified_at' => now(),
        ]);

        $this->call(PostSeeder::class);
    }
}
