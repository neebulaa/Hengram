<?php

namespace App\Http\Resources;

use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowingUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            "id" => $this->id,
            "full_name" => $this->full_name,
            "username" => $this->username,
            "bio" => $this->bio,
            "is_private" => $this->is_private,
            "created_at" => $this->created_at,
        ];

        if ($this->is_private) {
            $follow = Follow::where('following_id', $this->id)->where('follower_id', auth()->user()->id)->first();
            $response['is_requested'] = !($follow && $follow->is_accepted) ? true : false;
        };

        return $response;
    }
}
