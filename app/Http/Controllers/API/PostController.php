<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\PostRequest;
use App\Post;
use Illuminate\Http\Request;

class PostController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Posts retrieved successfully.');
    }

    public function store(PostRequest $request)
    {
        $input = $request->all();
        $input['slug'] = str_slug($input['name']);
        $entity = Post::create($input);
        return $this->sendResponse($entity->toArray(), 'Post created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Post::find($id);
        if (is_null($entity)) {
            return $this->sendError('Post not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Post retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Post::select();
            if(isset($input['trash'])) {
                $entity = $entity->onlyTrashed();
            }
            if (isset($input['offset'])) {
                $entity = $entity->offset($input['offset']);
            }
            if (isset($input['limit'])) {
                $entity = $entity->limit($input['limit']);
            }
            if (isset($input['where'])) {
                $entity = $entity->whereRaw($input['where']);
            }
            if (isset($input['order'])) {
                $entity = $entity->orderByRaw($input['order']);
            }
            return $entity->with('top')->get();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function update(PostRequest $request, $id)
    {
        $input = $request->all();
        $input['slug'] = str_slug($input['name']);
        $entity  = Post::find($id);
        if (is_null($entity)) {
            return $this->sendError('Post not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Post updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Post::find($id);
        if (is_null($entity)) {
            return $this->sendError('Post not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Post deleted successfully.');
    }
}
