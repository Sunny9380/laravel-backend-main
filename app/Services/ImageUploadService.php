<?php

namespace App\Services;

class ImageUploadService
{
    private $image;
    private $path;

    public function uploadImage($image, $path)
    {
        if ($image) {
            // Generate a unique name for the file
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            // Store the file in the 'public' disk under the 'files' directory
            $image->storeAs($path, $fileName, 'public');
            return $fileName;
        } else {
            return $image;
        }
    }

    public function uploadImageSet($images, $path)
    {
        $newImages = [];

        foreach ($images as $key => $image) {
            $newImages[] = $this->uploadImage($image, $path);
        }

        return json_encode($newImages);
    }

    public function deleteImageSet($imagesJson, $path)
    {
        $images = json_decode($imagesJson);
        foreach ($images as $image) {
            $this->deleteImage($image, $path);
        }
    }

    public function updateImage($oldImage, $newImage, $path)
    {
        $this->deleteImage($oldImage, $path);
        return $this->uploadImage($newImage, $path);
    }

    public function deleteImage($image, $path)
    {
        if ($image) {
            $this->path = public_path('storage/' . $path . $image);
            if (file_exists($this->path)) {
                unlink($this->path);
            }
        }
    }

    public function validateImage($image)
    {

        if ($image) {
            $ext = $image->getClientOriginalExtension();
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                return false;
            }

            //checking image size
            if ($image->getSize() > 500 * 1024) {
                return false;
            }
        }
        return true;
    }

    public function validateImages($images)
    {
        foreach ($images as $image) {
            return $this->validateImage($image);
        }
        return true;
    }
}
