<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\PostAttachment;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "page" => "integer|min:0",
            "size" => "integer|min:1"
        ]);

        if ($validator->fails()) {
            return response([
                "message" => "Invalid fields",
                "errors" => $validator->errors()
            ]);
        }

        // default value
        $validated_data = $validator->validated();
        if (!isset($validated_data['size'])) {
            $validated_data['size'] = 10;
        }

        if (!isset($validated_data['page'])) {
            $validated_data['page'] = 0;
        }

        $user = auth()->user();

        $posts = Post::with(['user', "attachments:id,post_id,storage_path"])->get()->filter(function ($post) use ($user) {
            return $post->user->id == $user->id || !$post->user->is_private || $post->user->acceptsFollowFrom($user->id);
        })->slice($validated_data['page'] * $validated_data['size'], $validated_data['size'])->values();

        return response([
            "page" => $validated_data['page'],
            "size" => $validated_data['size'],
            "posts" => $posts
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
            ]);
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
