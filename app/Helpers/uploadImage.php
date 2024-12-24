<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

if (! function_exists('uploadFile')) {
    function uploadFile($file, $path = ''): string|null
    {
        if (!$file) {
            return null;
        }

        $ext = $file->getClientOriginalExtension();
        $hash = fake()->sha1();
        return URL::to('/') . '/storage/' . Storage::disk('public')->putFileAs($path, $file, "$hash.$ext");
    }
}


