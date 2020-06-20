<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Characteristic;
use App\Http\Requests\CategoryCharacteristicRequest;
use Illuminate\Http\Request;

class CategoryCharacteristicController extends ApiController
{
//
//    public function index(CategoryCharacteristicRequest $request)
//    {
//        $input = $request->all();
//        $category = Category::find($input['category_id']);
//        $characteristic = Characteristic::find($input['characteristic_id']);
//        $response = ['category' => $category, 'characteristic' => $characteristic];
//        return $this->sendResponse($response, 'CategoryCharacteristics retrieved successfully.');
//    }
//
//    public function store(CategoryCharacteristicRequest $request)
//    {
//        $input = $request->all();
//        $category = Category::find($input['category_id']);
//        $characteristic = Characteristic::find($input['characteristic_id']);
//        if (!$this->isAtach($category, $characteristic)) {
//            $category->characteristics()->attach($characteristic);
//        }
//        $response = ['category' => $category, 'characteristic' => $characteristic];
//        return $this->sendResponse($response, 'CategoryCharacteristic created successfully.');
//    }
//
//    private function isAtach($category, $characteristicChecked)
//    {
//        if ($category->characteristics != null) {
//            foreach ($category->characteristics as $characteristic) {
//                if ($characteristicChecked->id == $characteristic->id) {
//                    return true;
//                }
//            }
//            return false;
//        } else {
//            return true;
//        }
//    }
//
//    public function destroy(CategoryCharacteristicRequest $request, $id)
//    {
//        $input = $request->all();
//        $category = Category::find($input['category_id']);
//        $characteristic = Characteristic::find($input['characteristic_id']);
//        $category->characteristics()->detach($characteristic);
//        $response = ['category' => $category, 'characteristic' => $characteristic];
//        return $this->sendResponse($response, 'CategoryCharacteristic deleted successfully.');
//    }


    public function update(CategoryCharacteristicRequest $request, $id)
    {
        $input = $request->all();
        $category = Category::find($id);
        if(!isset($input['characteristics'])) $input['characteristics'] = [];
        $category->characteristics()->sync($input['characteristics']);
        $response = ['category' => $category, 'characteristics' => $category->characteristics];
        return $this->sendResponse($response, 'CategoryCharacteristic created successfully.');
    }
}
