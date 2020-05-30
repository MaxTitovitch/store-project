<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\LikeRequest;
use App\Like;
use Illuminate\Http\Request;

class LikeController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Likes retrieved successfully.');
    }

    public function store(LikeRequest $request)
    {
        $input = $request->all();
        $entity = Like::create($input);
        return $this->sendResponse($entity->toArray(), 'Like created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Like::find($id);
        if (is_null($entity)) {
            return $this->sendError('Like not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Like retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Like::select();
//        if(isset($input['trash'])) {
//            $entity = $entity->onlyTrashed();
//        }
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

    public function update(LikeRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Like::find($id);
        if (is_null($entity)) {
            return $this->sendError('Like not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Like updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Like::find($id);
        if (is_null($entity)) {
            return $this->sendError('Like not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Like deleted successfully.');
    }
}
