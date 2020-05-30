<?php

namespace App\Http\Controllers\API;

use App\Address;
use App\Http\Requests\AddressRequest;

class AddressController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Addresses retrieved successfully.');
    }

    public function store(TagRequest $request)
    {
        $input = $request->all();
        $entity = Address::create($input);
        return $this->sendResponse($entity->toArray(), 'Address created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Address::find($id);
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Address retrieved successfully.');
    }

    private function filtrateQuery($input){
        $entity = Address::select();
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
        $entity  = Address::find($id);
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Address updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Address::find($id);
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Address deleted successfully.');
    }
}                                                                                                                                                           