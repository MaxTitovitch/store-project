<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CharacteristicRequest;
use App\Characteristic;
use Illuminate\Http\Request;

class CharacteristicController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Characteristics retrieved successfully.');
    }

    public function store(CharacteristicRequest $request)
    {
        $input = $request->all();
        $entity = Characteristic::create($input);
        return $this->sendResponse($entity->toArray(), 'Characteristic created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Characteristic::find($id);
        if (is_null($entity)) {
            return $this->sendError('Characteristic not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Characteristic retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Characteristic::select();
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
            return $entity->with('characteristicValues')->get();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function update(CharacteristicRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Characteristic::find($id);
        if (is_null($entity)) {
            return $this->sendError('Characteristic not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Characteristic updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Characteristic::find($id);
        if (is_null($entity)) {
            return $this->sendError('Characteristic not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Characteristic deleted successfully.');
    }
}
