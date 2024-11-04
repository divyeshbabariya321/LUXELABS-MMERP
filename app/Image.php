<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request as Input;

class Image extends Model
{
    public static function generateImageName($key)
    {
        $name      = Input::file($key)->getClientOriginalName();
        $extension = Input::file($key)->getClientOriginalExtension();
        $timestamp = date('Y-m-d-His', time());

        return $name . '-' . $timestamp . '.' . $extension;
    }

    public static function newImage($key = 'image')
    {
        $image_name = self::generateImageName($key);
        $imageFile  = Input::file($key);
        Storage::disk('s3')->put(config('constants.default_uploads_dir') . $image_name, file_get_contents($imageFile->getRealPath()));

        return $image_name;
    }

    public static function replaceImage($imageName, $key = 'image')
    {
        $sourcePath      = config('constants.default_uploads_dir') . $imageName;
        $destinationPath = config('constants.default_archive__dir') . $imageName;

        Storage::disk('s3')->move($sourcePath, $destinationPath);

        return self::newImage($key);
    }

    public static function trashImage($imageName)
    {
        $sourcePath      = config('constants.default_uploads_dir') . $imageName;
        $destinationPath = config('constants.default_trash__dir') . $imageName;

        Storage::disk('s3')->move($sourcePath, $destinationPath);
    }

    public function schedule(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ImageSchedule::class, 'image_id', 'id');
    }
}
