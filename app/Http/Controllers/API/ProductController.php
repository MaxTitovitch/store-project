<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ProductRequest;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\View;

class ProductController extends ApiController {

    public function index(Request $request) {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities, 'Products retrieved successfully.');
    }

    public function store(ProductRequest $request) {
        $input = $request->all();
        $input['slug'] = str_slug($input['name']);
        $entity = Product::create($input);
        return $this->sendResponse($entity->toArray(), 'Product created successfully.');
    }

    public function show(Request $request, $id) {
        $entity = Product::with([
            'category',
            'product_characteristics',
            'product_characteristics.characteristic',
            'sales'=>function ($query) {
            $date = date('Y-m-d');
                $query->whereRaw("sales.date_start < '$date' AND sales.date_end > '$date'");
            }
        ])->find($id);
        if (is_null($entity)) {
            return $this->sendError('Product not found.');
        }
        $this->createView($entity->id);
        return $this->sendResponse($entity->toArray(), 'Product retrieved successfully.');
    }

    private function createView($id) {
        $userId = auth('api')->user() ? auth('api')->user()->id : null;
        View::create(['entity_type' => 'product', 'entity_id' => $id, 'date' => date('Y-m-d'), 'user_id' => $userId,]);
    }

    public function showCharacteristic($id) {
        //        $product  = Product::where('id', $id)->with('category')->first();
        //        if (is_null($product)) {
        //            return $this->sendError('Product not found.');
        //        }
        //        $entity = $product->category->characteristics;
        //        $characteristicArray = [];
        //        foreach ($entity as $characteristic) {
        //            $isHave = false;
        //            foreach ($characteristic->productCharacteristics as $productCharacteristic) {
        //                if($product->id == $productCharacteristic->product_id)  {
        //                    $isHave = true;
        //                }
        //            }
        //            if(!$isHave) $characteristicArray[] = $characteristic;
        //        }
        //        return $this->sendResponse($characteristicArray, 'Product\'s characteristic retrieved successfully.');

        $entity = Product::where('id', $id)->with('category')->first();
        if (is_null($entity)) {
            return $this->sendError('Product not found.');
        }
        $entity = $entity->category->characteristics;
        $entityArray = $entity->toArray();
        for ($i = 0; $i < count($entity); $i++) {
            $entityArray[$i]['values'] = $entity[$i]->characteristicValues;
        }
        return $this->sendResponse($entity->toArray(), 'Product\'s characteristic retrieved successfully.');
    }

    private function filtrateQuery($input) {
        try {
            $entity = Product::select();
            if (isset($input['trash'])) {
                $entity = $entity->onlyTrashed();
            }
            if (isset($input['offset'])) {
                $entity = $entity->offset($input['offset']);
            }
            if (isset($input['withCount'])) {
                $entity = $entity->withCount($input['withCount']);
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
            if (isset($input['with'])) {
                if (isset($input['withWhere'])) {
                    $entity = $entity->with([$input['with'] => function ($query) use ($input) {
                        $query->whereRaw($input['withWhere']);
                    }]);
                } else {
                    $entity = $entity->with($input['with']);
                }
            }
            if (isset($input['count'])) {
                return $entity->count();
            }
            return $entity->with('tags')->get()->toArray();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function update(ProductRequest $request, $id) {
        $input = $request->all();
        $input['slug'] = str_slug($input['name']);
        $entity = Product::find($id);
        if (is_null($entity)) {
            return $this->sendError('Product not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Product updated successfully.');
    }

    public function destroy($id) {
        $entity = Product::find($id);
        if (is_null($entity)) {
            return $this->sendError('Product not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Product deleted successfully.');
    }
}
