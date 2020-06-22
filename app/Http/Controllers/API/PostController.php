<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\PostRequest;
use App\Post;
use Illuminate\Http\Request;
use App\View;
use Illuminate\Support\Facades\Auth;

class PostController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities, 'Posts retrieved successfully.');
    }

    public function store(PostRequest $request)
    {
        $input = $request->all();
        $input['slug'] = str_slug($input['name']);
        $entity = Post::create($input);
        return $this->sendResponse($entity->toArray(), 'Post created successfully.');
    }

    private function createView($id){
        $userId = auth('api')->user() ? auth('api')->user()->id : null;
        View::create([
            'entity_type' => 'post',
            'entity_id' => $id,
            'date' => date ('Y-m-d'),
            'user_id' => $userId,
        ]);
    }

    public function show(Request $request, $id)
    {
        $entity  = Post::with(['user', 'top', 'top.products'])->find($id);

        if (is_null($entity)) {
            return $this->sendError('Post not found.');
        }
        $this->createView($entity->id);
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
            if (isset($input['count'])) {
                return $entity->count();
            }
            return $entity->with('top')->get()->toArray();
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
