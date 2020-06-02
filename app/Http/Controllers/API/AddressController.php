<?php

namespace App\Http\Controllers\API;

use App\Address;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;

class AddressController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Addresses retrieved successfully.');
    }

    public function indexUser(Request $request)
    {
        $entities = $this->filtrateQuery($request->all(), true);
        return $this->sendResponse($entities->toArray(), 'Addresses retrieved successfully.');
    }

    public function store(AddressRequest $request)
    {
        $input = $request->all();
        $entity = Address::create($input);
        return $this->sendResponse($entity->toArray(), 'Address created successfully.');
    }

    public function storeUser(AddressRequest $request)
    {
        $user = auth('api')->user();
        $input = $request->all();
        $input['user_id'] = $user->id;
        $entity = Address::create($input);
        return $this->sendResponse($entity->toArray(), 'Address created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity = Address::find($id);
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Address retrieved successfully.');
    }

    public function showUser(Request $request, $id)
    {
        $user = auth('api')->user();
        $entity = Address::where('user_id', $user->id)->where('id', $id)->first();
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Address retrieved successfully.');
    }

    private function filtrateQuery($input, $isForUser = false)
    {
        try {
            $entity = Address::select();

            if (isset($input['offset'])) {
                $entity = $entity->offset($input['offset']);
            }
            if (isset($input['limit'])) {
                $entity = $entity->limit($input['limit']);
            }
            if (isset($input['where'])) {
                $entity = $entity->whereRaw($input['where']);
            }
            if ($isForUser) {
                $entity = $entity->whereRaw('user_id = ' . auth('api')->user()->id);
            }
            if (isset($input['order'])) {
                $entity = $entity->orderByRaw($input['order']);
            }
            return $entity->get();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function update(AddressRequest $request, $id)
    {
        $input = $request->all();
        $entity = Address::find($id);
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Address updated successfully.');
    }

    public function updateUser(AddressRequest $request, $id)
    {
        $input = $request->all();
        $user = auth('api')->user();
        $entity = Address::where('user_id', $user->id)->where('id', $id)->first();
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Address updated successfully.');
    }

    public function destroy($id)
    {
        $entity = Address::find($id);
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Address deleted successfully.');
    }

    public function destroyUser($id)
    {
        $user = auth('api')->user();
        $entity = Address::where('user_id', $user->id)->where('id', $id)->first();
        if (is_null($entity)) {
            return $this->sendError('Address not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Address deleted successfully.');
    }
}