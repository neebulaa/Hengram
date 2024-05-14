<?php

namespace Database\Seeders;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Models\PostAttachment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\PostFactory;

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
            "username" => "john.doe",
            "password" => "pass123",
            "bio" => "Let your light shine bright and inspire others.",
            "full_name" => "John Doe",
            "is_private" => false
        ]);

        User::create([
            "full_name" => "Richard Roe",
            "username" => "richard.roe",
            "bio" => "In a world where you can be anything, be kind.",
            "password" => "pass123",
            "is_private" => true
        ]);

        User::create([
            "full_name" => "Arielle Reichert",
            "username" => "arielle",
            "bio" => "Life is a beautiful journey, embrace it.",
            "password" => "pass123",
            "is_private" => true
        ]);

        User::create([
            "full_name" => "Prof. Idell Renner",
            "username" => "idellener",
            "password" => "pass123",
            "bio" => "Be fearless in the pursuit of what sets your soul on fire.",
            "is_private" => false
        ]);

        User::create([
            "full_name" => "Teagan Kautzer",
            "username" => "kautzer",
            "password" => "pass123",
            "bio" => "Success is not final, failure is not fatal: It is the courage to continue that counts.",
            "is_private" => true
        ]);

        Follow::create([
            "follower_id" => 1,
            "following_id" => 2,
            "is_accepted" => false 
        ]);

        Follow::create([
            "follower_id" => 2,
            "following_id" => 5,
            "is_accepted" => true 
        ]);

        Follow::create([
            "follower_id" => 2,
            "following_id" => 4,
            "is_accepted" => true 
        ]);

        Follow::create([
            "follower_id" => 3,
            "following_id" => 1,
            "is_accepted" => true 
        ]);

        Follow::create([
            "follower_id" => 4,
            "following_id" => 5,
            "is_accepted" => false 
        ]);

        Post::factory(30)->create()->each(function ($post) {
            $post->attachments()->createMany(
                PostAttachment::factory(random_int(1, 4))->make()->toArray()
            );
        });
    }
}
