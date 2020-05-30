<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ViewRequest;
use App\View;
use Illuminate\Http\Request;

class ViewController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Views retrieved successfully.');
    }

    public function store(ViewRequest $request)
    {
        $input = $request->all();
        $entity = View::create($input);
        return $this->sendResponse($entity->toArray(), 'View created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = View::find($id);
        if (is_null($entity)) {
            return $this->sendError('View not found.');
        }
        return $this->sendResponse($entity->toArray(), 'View retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = View::select();
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

    public function update(ViewRequest $request, $id)
    {
        $input = $request->all();
        $entity  = View::find($id);
        if (is_null($entity)) {
            return $this->sendError('View not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'View updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = View::find($id);
        if (is_null($entity)) {
            return $this->sendError('View not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'View deleted successfully.');
    }
}
