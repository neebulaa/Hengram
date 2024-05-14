<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PostAttachment;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostAttachmentResource;

class PostController extends Controller
{
    public function index(Request $request, User $user = null)
    {
        $validator = Validator::make($request->all(), [
            "page" => "integer|min:0",
            "size" => "integer|min:1"
        ]);

        if ($validator->fails()) {
            return response([
                "message" => "Invalid fields",
                "errors" => $validator->errors()
            ], 422);
        }

        // default value
        $validated_data = $validator->validated();
        if (!isset($validated_data['size'])) {
            $validated_data['size'] = 10;
        }

        if (!isset($validated_data['page'])) {
            $validated_data['page'] = 0;
        }

        $validated_data['size'] = intval($validated_data['size']);
        $validated_data['page'] = intval($validated_data['page']);

        $user = $user ?? auth()->user();

        $posts = Post::with(['user', "attachments:id,post_id,storage_path"])->latest()->get()->filter(function ($post) use ($user) {
            return $post->user->id == $user->id || !$post->user->is_private || $post->user->acceptsFollowFrom($user->id);
        });
        $slicedPosts = $posts->slice($validated_data['page'] * $validated_data['size'], $validated_data['size'])->values();

        $updatedPosts = $slicedPosts->toArray();
        foreach ($updatedPosts as &$post) {
            $post['attachments'] = array_map(function ($attachment) {
                return new PostAttachmentResource(new PostAttachment($attachment));
            }, $post['attachments']);
        }

        return response([
            "total_page" => ceil($posts->count() / $validated_data['size']),
            "page" => $validated_data['page'],
            "size" => $validated_data['size'],
            "posts" => $updatedPosts
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "caption" => "required",
            "attachments" => "required|array",
            "attachments.*" => "image|file|max:2048|mimes:jpeg,jpg,png,webp,gif"
        ]);

        if ($validator->fails()) {
            return response([
                "message" => "Invalid fields",
                "errors" => $validator->errors()
            ], 422);
        }

        $validated_data = $validator->validated();
        $post = Post::create([
            "caption" => $validated_data['caption'],
            "user_id" => auth()->user()->id
        ]);

        $attachments = $request->file('attachments');
        foreach ($attachments as $attachment) {
            $attachment_path = $attachment->store("posts");
            PostAttachment::create([
                "storage_path" => $attachment_path,
                "post_id" => $post->id
            ]);
        }

        return response([
            "message" => "Create post success"
        ]);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response([
            "message" => "Delete post success"
        ]);
    }
}
