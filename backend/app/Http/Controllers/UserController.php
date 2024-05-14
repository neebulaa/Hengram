<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Models\PostAttachment;
use App\Http\Resources\PostAttachmentResource;

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

    public function show(Request $request, User $user)
    {
        // $posts = app('App\Http\Controllers\PostController')->index($request, $user)->original['posts'];
        $posts = $user->posts->load(['user', 'attachments:id,storage_path,post_id'])->sortByDesc('created_at')->values();
        $posts = $posts->toArray();
        foreach ($posts as &$post) {
            $post['attachments'] = array_map(function ($attachment) {
                return new PostAttachmentResource(new PostAttachment($attachment));
            }, $post['attachments']);
        }

        $posts = collect($posts);
        $response = [
            ...$user->toArray(),
            "is_your_account" => auth()->user()->id == $user->id,
            "posts_count" => $posts->count(),
            "followers_count" => $user->followers->count(),
            "following_count" => $user->following_users->count(),
            "posts" => $posts
        ];

        $follow = Follow::where("follower_id", auth()->user()->id)->where("following_id", $user->id)->first();
        if (!$follow) {
            $response['following_status'] = "not-following";
        } else {
            if (!$follow->is_accepted) {
                $response['following_status'] = "requested";
            } else {
                $response['following_status'] = "following";
            }
        }

        return response([
            "message" => "Get user success",
            "user" => $response
        ]);
    }

    public function getAllUsers()
    {
        return [
            "message" => "Get all users success",
            "users" => User::where("id", "!=", auth()->user()->id)->get()
        ];
    }
}
