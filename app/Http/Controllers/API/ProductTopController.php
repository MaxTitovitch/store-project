<?php

namespace App\Http\Controllers\API;

use App\Product;
use App\Top;
use App\Http\Requests\ProductTopRequest;
use Illuminate\Http\Request;

class ProductTopController extends ApiController
{

//    public function index(ProductTopRequest $request)
//    {
//        $input = $request->all();
//        $product = Product::find($input['product_id']);
//        $top = Top::find($input['top_id']);
//        $response = ['product' => $product, 'top' => $top];
//        return $this->sendResponse($response, 'ProductTops retrieved successfully.');
//    }

//    public function store(ProductTopRequest $request)
//    {
//        $input = $request->all();
//        $product = Product::find($input['product_id']);
//        $top = Top::find($input['top_id']);
//        if(!$this->isAtach($product, $top)) {
//            $product->tops()->attach($top);
//        }
//        $response = ['product' => $product, 'top' => $top];
//        return $this->sendResponse($response, 'ProductTop created successfully.');
//    }

//    private function isAtach($product, $topChecked) {
//        if($product->tops != null) {
//            foreach ($product->tops as $top) {
//                if ($topChecked->id == $top->id) {
//                    return true;
//                }
//            }
//            return false;
//        } else {
//            return true;
//        }
//    }
//
//    public function destroy(ProductTopRequest $request, $id)
//    {
//        $input = $request->all();
//        $product = Product::find($input['product_id']);
//        $top = Top::find($input['top_id']);
//        $product->tops()->detach($top);
//        $response = ['product' => $product, 'top' => $top];
//        return $this->sendResponse($response, 'ProductTop deleted successfully.');
//    }

    public function update(ProductTopRequest $request, $id)
    {
        $input = $request->all();
        $top = Top::find($id);
        if(!isset($input['products'])) $input['products'] = [];
        $products = [];
        for($i = 0; $i < count($input['products']); $i++) {
            $products[$input['products'][$i]] = ['top'=>$i + 1];
        }
        try {
            $top->products()->sync($products);
        } catch (\Exception $e) {
            return $this->sendError($products, 'jj');
        }
        $response = ['top' => $top, 'products' => $top->products];
        return $this->sendResponse($response, 'ProductTop created successfully.');
    }
}
