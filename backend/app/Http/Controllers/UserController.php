<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all()->filter(function ($user) {
            $follow = Follow::where('following_id', $user->id)->where('follower_id', auth()->user()->id)->first();
            return auth()->user()->id != $user->id && !$follow;
        })->values();

        return response([
            "message" => "Get users success",
            "users" => $users
        ]);
    }

    public function show(User $user)
    {
        $posts = $user->posts;
        $response = [
            ...$user->toArra(),
            "is_your_account" => auth()->user()->id == $user->id,
            "posts_count" => $posts->count(),
            "followers_count" => $user->followers()->count(),
            "following_count" => $user->following_users()->count(),
            "posts" => $posts
        ];

        if (auth()->user()->id != $user->id) {
            $response['following_status'] = "not-following";
        }

        return response([
            "message" => "Get user success",
            "user" => $response
        ]);
    }
}
