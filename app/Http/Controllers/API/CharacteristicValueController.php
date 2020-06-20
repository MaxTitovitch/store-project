<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CharacteristicValueRequest;
use App\CharacteristicValue;
use Illuminate\Http\Request;

class CharacteristicValueController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Characteristic value values retrieved successfully.');
    }

    public function store(CharacteristicValueRequest $request)
    {
        $input = $request->all();
        $entity = CharacteristicValue::create($input);
        return $this->sendResponse($entity->toArray(), 'Characteristic value created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = CharacteristicValue::find($id);
        if (is_null($entity)) {
            return $this->sendError('Characteristic value not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Characteristic value retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = CharacteristicValue::select();
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

    public function update(CharacteristicValueRequest $request, $id)
    {
        $input = $request->all();
        $entity  = CharacteristicValue::find($id);
        if (is_null($entity)) {
            return $this->sendError('Characteristic value not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Characteristic value updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = CharacteristicValue::find($id);
        if (is_null($entity)) {
            return $this->sendError('Characteristic value not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Characteristic value deleted successfully.');
    }

    public function destroyByCharacteristic($id)
    {
        $entity  = CharacteristicValue::where('characteristic_id', $id)->delete();
        return $this->sendResponse($entity, 'Characteristic value deleted successfully.');
    }
}
