<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\PhotoRequest;
use App\Post;
use Illuminate\Support\Facades\Storage;

class PhotoController extends ApiController
{
    public function createPostPhoto(PhotoRequest $request) {
        $deleteSlug  = $request->get('delete-slug');
        $file = $request->file('image');
        if(!empty($deleteSlug)) {
            Storage::delete('public/posts/' .$deleteSlug . '.png');
        }
        $path = Storage::disk('public')->putFileAs( $request->get('type'), $file, $request->get('slug') . '.png');
        $path = env('APP_URL') . '/storage/' . $path;
        return $this->sendResponse($path, 'File has saved successfully.');
    }

}
