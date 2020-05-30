<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\SaleCategoryRequest;
use App\SaleCategory;
use Illuminate\Http\Request;

class SaleCategoryController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Sale categories retrieved successfully.');
    }

    public function store(SaleCategoryRequest $request)
    {
        $input = $request->all();
        $entity = SaleCategory::create($input);
        return $this->sendResponse($entity->toArray(), 'Sale category created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = SaleCategory::find($id);
        if (is_null($entity)) {
            return $this->sendError('SaleCategory not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Sale category retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = SaleCategory::select();
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

    public function update(SaleCategoryRequest $request, $id)
    {
        $input = $request->all();
        $entity  = SaleCategory::find($id);
        if (is_null($entity)) {
            return $this->sendError('SaleCategory not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Sale category updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = SaleCategory::find($id);
        if (is_null($entity)) {
            return $this->sendError('SaleCategory not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Sale category deleted successfully.');
    }
}
