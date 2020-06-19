<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\TopRequest;
use App\Top;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities, 'Tops retrieved successfully.');
    }

    public function store(TopRequest $request)
    {
        $input = $request->all();
        $entity = Top::create($input);
        return $this->sendResponse($entity->toArray(), 'Top created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Top::find($id);
        if (is_null($entity)) {
            return $this->sendError('Top not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Top retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Top::select();
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
            $data = $entity->get();
            $array = $data->toArray();
            for ($i = 0; $i < count($data); $i++) {
                $array[$i]['products'] = DB::table('product_top')
                    ->selectRaw('products.*, product_top.id as main_id')
                    ->where('product_top.top_id', '=', $array[$i])
                    ->join('products', 'products.id', '=', 'product_top.product_id')
                    ->orderByRaw('main_id desc')->get();
            }
            return $array;

        } catch (\Exception $ex) {
            return null;
        }
    }

    public function update(TopRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Top::find($id);
        if (is_null($entity)) {
            return $this->sendError('Top not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Top updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Top::find($id);
        if (is_null($entity)) {
            return $this->sendError('Top not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Top deleted successfully.');
    }
}
