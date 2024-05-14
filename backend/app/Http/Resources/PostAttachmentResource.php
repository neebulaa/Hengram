<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostAttachmentResource extends JsonResource
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
            "storage_path" => $this->storage_path,
            "post_id" => $this->post_id,
            "full_path" => asset("images/{$this->storage_path}")
        ];
        return $response;
    }
}
