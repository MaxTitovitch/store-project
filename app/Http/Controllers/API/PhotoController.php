<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\PhotoRequest;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Storage;

class PhotoController extends ApiController
{
    public function createPhoto(PhotoRequest $request) {
        $file = $request->file('image');
        $deleteSlug  = $request->get('delete-slug');
        $slug  = $request->get('slug');
        $type  = $request->get('type');

        return $this->storePhoto($file, $type, $slug, $deleteSlug);
    }

    public function createUserPhoto(PhotoRequest $request, $id) {
        $file = $request->file('image');
        $deleteSlug  = $request->get('delete-slug');
        $slug  = $request->get('slug');
        $type  = 'users';
        $user = User::find($id);
        if($user == null){
            return $this->sendError('Store error', 'Users isn\'t found.');
        }
        if($user->slug != $slug) {
            return $this->sendError('Store error', 'It isn\'t your photo.');
        }
        return $this->storePhoto($file, $type, $slug, $deleteSlug);
    }

    private function storePhoto($file, $type, $slug, $deleteSlug) {

        if(!empty($deleteSlug)) {
            Storage::delete("public/$type/" .$deleteSlug . '.png');
        }
        $path = Storage::disk('public')->putFileAs($type, $file, $slug . '.png');
        $path = env('APP_URL') . '/storage/' . $path;
        return $this->sendResponse($path, 'File has saved successfully.');

    }


}
