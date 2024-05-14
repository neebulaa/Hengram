<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Http\Resources\FollowerUserResource;
use App\Http\Resources\FollowingUserResource;

class FollowController extends Controller
{
    public function follow(Request $request, User $user)
    {
        if ($user->id == auth()->user()->id) {
            return response([
                "message" => "You are not allowed to follow yourself",
            ], 422);
        }

        $follow = Follow::where('follower_id', auth()->user()->id)->where('following_id', $user->id)->first();
        if ($follow) {
            return response([
                "message" => "You are already followed",
                "status" => $this->getFollowerStatus($follow->follower_id, $follow->following_id)
            ]);
        }

        Follow::create([
            "following_id" => $user->id,
            "follower_id" => auth()->user()->id,
            "is_accepted" => $user->is_private ? false : true
        ]);

        return response([
            "message" => "Follow success",
            "status" => $user->is_private ? "requested" : "following"
        ]);
    }

    public function unfollow(Request $request, User $user)
    {
        $follow = Follow::where('follower_id', auth()->user()->id)->where('following_id', $user->id)->first();
        if (!$follow) {
            return response([
                "message" => "You are not following this user"
            ], 422);
        }

        $follow->delete();
        return response([
            "message" => "Unfollow success"
        ]);
    }

    public function getFollowerStatus($follower_id, $following_id)
    {
        $follow = Follow::where('follower_id', $follower_id)->where('following_id', $following_id)->first();
        if (!$follow) {
            return "not-following";
        }
        $following_user = $follow->following_user;

        if (!$following_user->is_private || $follow->is_accepted) {
            return "following";
        } else {
            return "requested";
        }
    }

    public function following(User $user)
    {
        $following_users = $user->following_users;
        return response([
            "message" => "Get following users success",
            "following" => FollowingUserResource::collection($following_users)
        ]);
    }

    public function accept(User $user)
    {
        if (!auth()->user()->is_private) {
            return response([
                "message" => "Your account is not private"
            ], 422);
        }

        $follow = Follow::where('following_id', auth()->user()->id)->where('follower_id', $user->id)->first();

        if (!$follow) {
            return response([
                "message" => "The user is not following you"
            ], 422);
        }

        if ($follow->is_accepted) {
            return response([
                "message" => "Follow request is already accepted"
            ], 422);
        }

        $follow->is_accepted = true;
        $follow->save();
        return response([
            "message" => "Follow request accepted"
        ]);
    }

    public function followers(User $user)
    {
        $followers = $user->followers;
        return response([
            "message" => "Get followers success",
            "followers" => FollowerUserResource::collection($followers)
        ]);
    }
}
