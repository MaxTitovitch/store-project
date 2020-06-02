<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Users retrieved successfully.');
    }

    public function store(UserRequest $request)
    {
        $input = $request->all();
        $entity = User::create($input);
        return $this->sendResponse($entity->toArray(), 'User created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = User::find($id);
        if (is_null($entity)) {
            return $this->sendError('User not found.');
        }
        return $this->sendResponse($entity->toArray(), 'User retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = User::select();
            if(isset($input['trash'])) {
                $entity = $entity->onlyTrashed();
            }
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

    public function update(UserRequest $request, $id)
    {
        $input = $request->all();
        $entity  = User::find($id);
        if (is_null($entity)) {
            return $this->sendError('User not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'User updated successfully.');
    }

    public function updateUser(UserRequest $request, $id)
    {
        $input = $request->all();
        $entity = auth('api')->user();

        if (is_null($entity)) {
            return $this->sendError('User not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'User updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = User::find($id);
        if (is_null($entity)) {
            return $this->sendError('User not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'User deleted successfully.');
    }

    public function destroyUser($id)
    {
        $entity = auth('api')->user();
        if (is_null($entity)) {
            return $this->sendError('User not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'User deleted successfully.');
    }
}
