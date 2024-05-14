<?php

namespace Database\Factories;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostAttachment>
 */
class PostAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $images = File::files('storage/app/server-images');
        $image = $images[random_int(0, count($images) - 1)];
        $randomFilename = uniqid() . '.' . $image->getExtension();

        $targetDirectory = 'images/posts/';
        if (!File::exists(public_path($targetDirectory))) {
            File::makeDirectory(public_path($targetDirectory), 0777, true, true);
        }

        File::copy($image->getPathname(), public_path($targetDirectory . $randomFilename));
        $uploadedImagePath = 'posts/' . $randomFilename;

        return [
            "post_id" => random_int(1, 30),
            "storage_path" => $uploadedImagePath
        ];
    }
}
