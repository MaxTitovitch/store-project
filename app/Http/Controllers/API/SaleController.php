<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\SaleRequest;
use App\Sale;
use Illuminate\Http\Request;

class SaleController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Sales retrieved successfully.');
    }

    public function store(SaleRequest $request)
    {
        $input = $request->all();
        $entity = Sale::create($input);
        return $this->sendResponse($entity->toArray(), 'Sale created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Sale::find($id);
        if (is_null($entity)) {
            return $this->sendError('Sale not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Sale retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Sale::select();
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

    public function update(SaleRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Sale::find($id);
        if (is_null($entity)) {
            return $this->sendError('Sale not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Sale updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Sale::find($id);
        if (is_null($entity)) {
            return $this->sendError('Sale not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Sale deleted successfully.');
    }
}
