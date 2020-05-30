<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ProductCharacteristicRequest;
use App\ProductCharacteristic;
use Illuminate\Http\Request;

class ProductCharacteristicController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Product characteristics retrieved successfully.');
    }

    public function store(ProductCharacteristicRequest $request)
    {
        $input = $request->all();
        $entity = ProductCharacteristic::create($input);
        return $this->sendResponse($entity->toArray(), 'Product characteristic created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = ProductCharacteristic::find($id);
        if (is_null($entity)) {
            return $this->sendError('ProductCharacteristic not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Product characteristic retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = ProductCharacteristic::select();
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

    public function update(ProductCharacteristicRequest $request, $id)
    {
        $input = $request->all();
        $entity  = ProductCharacteristic::find($id);
        if (is_null($entity)) {
            return $this->sendError('ProductCharacteristic not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Product characteristic updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = ProductCharacteristic::find($id);
        if (is_null($entity)) {
            return $this->sendError('ProductCharacteristic not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Product characteristic deleted successfully.');
    }
}
