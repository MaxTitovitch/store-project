<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\TagRequest;
use App\Tag;
use Illuminate\Http\Request;

class TagController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Tags retrieved successfully.');
    }

    public function store(TagRequest $request)
    {
        $input = $request->all();
        $entity = Tag::create($input);
        return $this->sendResponse($entity->toArray(), 'Tag created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Tag::find($id);
        if (is_null($entity)) {
            return $this->sendError('Tag not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Tag retrieved successfully.');
    }

    private function filtrateQuery($input){
        $entity = Tag::select();
//        if(isset($input['trash'])) {
//            $entity = $entity->onlyTrashed();
//        }
        if(isset($input['offset'])) {
            $entity = $entity->offset($input['offset']);
        }
        if(isset($input['limit'])) {
            $entity = $entity->limit($input['limit']);
        }
        if(isset($input['where'])) {
            $entity = $entity->whereRaw($input['where']);
        }
        if(isset($input['order'])) {
            $entity = $entity->orderByRaw($input['order']);
        }
        return $entity->get();
    }

    public function update(TagRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Tag::find($id);
        if (is_null($entity)) {
            return $this->sendError('Tag not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Tag updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Tag::find($id);
        if (is_null($entity)) {
            return $this->sendError('Tag not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Tag deleted successfully.');
    }
}
