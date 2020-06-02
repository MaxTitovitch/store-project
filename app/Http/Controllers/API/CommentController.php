<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CommentRequest;
use App\Comment;
use Illuminate\Http\Request;

class CommentController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Comments retrieved successfully.');
    }

    public function store(CommentRequest $request)
    {
        $input = $request->all();
        $entity = Comment::create($input);
        return $this->sendResponse($entity->toArray(), 'Comment created successfully.');
    }

    public function storeUser(CommentRequest $request)
    {
        $user = auth('api')->user();
        $input = $request->all();
        $input['user_id'] = $user->id;
        $entity = Comment::create($input);
        return $this->sendResponse($entity->toArray(), 'Comment created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Comment::find($id);
        if (is_null($entity)) {
            return $this->sendError('Comment not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Comment retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Comment::select();
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
            return $entity->get();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function update(CommentRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Comment::find($id);
        if (is_null($entity)) {
            return $this->sendError('Comment not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Comment updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Comment::find($id);
        if (is_null($entity)) {
            return $this->sendError('Comment not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Comment deleted successfully.');
    }

    public function destroyUser($id)
    {
        $user = auth('api')->user();
        $entity  = Comment::where('user_id', $user->id)->andWhere('id', $id)->first();
        if (is_null($entity)) {
            return $this->sendError('Comment not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Comment deleted successfully.');
    }
}
