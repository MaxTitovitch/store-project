<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\PhotoRequest;
use App\Post;
use Illuminate\Support\Facades\Storage;

class PhotoController extends ApiController
{
    public function createPostPhoto(PhotoRequest $request) {
        $slug  = $request->get('slug');
        $file = $request->file('image');
        $path = Storage::disk('public')->putFileAs( $request->get('type'), $file, $slug . '.png');
        $path = env('APP_URL') . '/storage/' . $path;
        return $this->sendResponse($path, 'File has saved successfully.');
    }

}
