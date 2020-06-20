<?php

namespace App\Http\Controllers\API;

use App\Product;
use App\Tag;
use App\Http\Requests\ProductTagRequest;
use Illuminate\Http\Request;

class ProductTagController extends ApiController
{

//    public function index(ProductTagRequest $request)
//    {
//        $input = $request->all();
//        $product = Product::find($input['product_id']);
//        $tag = Tag::find($input['tag_id']);
//        $response = ['product' => $product, 'tag' => $tag];
//        return $this->sendResponse($response, 'ProductTags retrieved successfully.');
//    }
//
//    public function store(ProductTagRequest $request)
//    {
//        $input = $request->all();
//        $product = Product::find($input['product_id']);
//        $tag = Tag::find($input['tag_id']);
//        if(!$this->isAtach($product, $tag)) {
//            $product->tags()->attach($tag);
//        }
//        $response = ['product' => $product, 'tag' => $tag];
//        return $this->sendResponse($response, 'ProductTag created successfully.');
//    }
//
//    private function isAtach($product, $tagChecked) {
//        if ($product->tags != null) {
//            foreach ($product->tags as $tag) {
//                if ($tagChecked->id == $tag->id) {
//                    return true;
//                }
//            }
//            return false;
//        } else {
//            return true;
//        }
//    }
//
//    public function destroy(ProductTagRequest $request, $id)
//    {
//        $input = $request->all();
//        $product = Product::find($input['product_id']);
//        $tag = Tag::find($input['tag_id']);
//        $product->tags()->detach($tag);
//        $response = ['product' => $product, 'tag' => $tag];
//        return $this->sendResponse($response, 'ProductTag deleted successfully.');
//    }

    public function update(ProductTagRequest $request, $id)
    {
        $input = $request->all();
        $product = Product::find($id);
        if(!isset($input['tags'])) $input['tags'] = [];
        $product->tags()->sync($input['tags']);
        $response = ['product' => $product, 'tags' => $product->tags];
        return $this->sendResponse($response, 'ProductTag created successfully.');
    }
}
