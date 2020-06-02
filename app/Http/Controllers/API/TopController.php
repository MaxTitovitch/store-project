<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\TopRequest;
use App\Top;
use Illuminate\Http\Request;

class TopController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Tops retrieved successfully.');
    }

    public function store(TopRequest $request)
    {
        $input = $request->all();
        $entity = Top::create($input);
        return $this->sendResponse($entity->toArray(), 'Top created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Top::find($id);
        if (is_null($entity)) {
            return $this->sendError('Top not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Top retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Top::select();
//            if(isset($input['trash'])) {
//                $entity = $entity->onlyTrashed();
//            }
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

    public function update(TopRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Top::find($id);
        if (is_null($entity)) {
            return $this->sendError('Top not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Top updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Top::find($id);
        if (is_null($entity)) {
            return $this->sendError('Top not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Top deleted successfully.');
    }
}
