<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UploadImageService
{
    function checkDirectoryIfExits($directory_name)
    {
        if (!Storage::disk('public')->exists($directory_name)) {
            Storage::disk('public')->makeDirectory($directory_name);
        }
    }

    function storeFile($directory_name, $image_name, $extension, $content)
    {
        Storage::disk('public')->put($directory_name . DIRECTORY_SEPARATOR . $image_name . '.' . $extension, $content);
    }

    function checkPathExitsThenDelete($directory_name, $image_name)
    {
        if (Storage::disk('public')->exists($directory_name . DIRECTORY_SEPARATOR . $image_name)) {
            Storage::disk('public')->delete($directory_name . DIRECTORY_SEPARATOR . $image_name);
        }
    }

    function copyFileAndRename($directory_name, $old_image_name, $old_extension, $new_image_name, $new_extension)
    {
        $old_path = $directory_name . DIRECTORY_SEPARATOR . $old_image_name . '.' . $old_extension;
        Storage::disk('public')->copy($old_path, $directory_name . DIRECTORY_SEPARATOR . $new_image_name . '.' . $new_extension);
    }
}
