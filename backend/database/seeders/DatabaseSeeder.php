<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Database\Factories\PostFactory;
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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            "username" => "richard.roe",
            "password" => "pass123",
            "bio" => "Hi i am passionate at coding",
            "full_name" => "Richard Roe",
            "is_private" => false
        ]);

        User::create([
            "username" => "john.doe",
            "password" => "pass123",
            "bio" => "I am john doee the doe 😏!",
            "full_name" => "John Doe",
            "is_private" => true
        ]);

        User::create([
            "username" => "budi.budiman",
            "password" => "pass123",
            "bio" => "I am budi 😏!",
            "full_name" => "Budi Budiman",
            "is_private" => false
        ]);

        Post::factory(30)->create();
    }
}
