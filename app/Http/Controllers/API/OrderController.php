<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\OrderRequest;
use App\Order;
use Illuminate\Http\Request;

class OrderController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Orders retrieved successfully.');
    }

    public function store(OrderRequest $request)
    {
        $input = $request->all();
        $entity = Order::create($input);
        return $this->sendResponse($entity->toArray(), 'Order created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Order::find($id);
        if (is_null($entity)) {
            return $this->sendError('Order not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Order retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Order::select();
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

    public function update(OrderRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Order::find($id);
        if (is_null($entity)) {
            return $this->sendError('Order not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Order updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Order::find($id);
        if (is_null($entity)) {
            return $this->sendError('Order not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Order deleted successfully.');
    }
}
