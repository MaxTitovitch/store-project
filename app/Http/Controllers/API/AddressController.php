<?php

namespace App\Http\Controllers\API;

use App\Address;
use App\Http\Requests\AddressRequest;

class AddressController extends ApiController
{

    public function index()
    {
        $entities = Address::all();
        return $this->sendResponse($entities->toArray(), 'Addresses retrieved successfully.');
    }

    public function store(AddressRequest $request)
    {
        $input = $request->all();
        $entity = Address::create($input);
        return $this->sendResponse($entity->toArray(), 'Address created successfully.');
    }

    public function show($id)
    {
        $entity = Address::find($id);
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Address retrieved successfully.');
    }

    public function update(AddressRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Address::find($id);
        $entity->name = $input['name'];
        $entity->name = $input['name'];
        $entity->name = $input['name'];
        $entity->name = $input['name'];
        $entity->name = $input['name'];
        $entity->save();
        return $this->sendResponse($entity->toArray(), 'Address updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Address::find($id);
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Address deleted successfully.');
    }
}
