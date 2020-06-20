<?php

namespace App\Http\Requests;

use App\Characteristic;
use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;
use App\ProductCharacteristic;
use Illuminate\Http\Request;

class ProductCharacteristicRequest extends FormRequest
{
    use ApiRequest;
    private $stringValues = '';
    private $intMax=0;
    private $intMin=10000;

    public function rules(Request $request)
    {
//        $id = $this->route()->parameters['product_characteristic'] ?? 0;
//        $productCharacteristic = ProductCharacteristic::find($id);
//        if($productCharacteristic != null) {
//            $characteristic = $productCharacteristic->characteristic;
//            if($characteristic->type == "number") {
//                $this->prepareNumberValues($characteristic);
//            } else if ($characteristic->type == "string") {
//                $this->prepareStringValues($characteristic);
//            }
//        }
        var_dump($request->all());
        $characteristic = Characteristic::find($request->all()['characteristic_id']);
        if($characteristic != null) {
            if($characteristic->type == "number") {
                $this->prepareNumberValues($characteristic);
            } else if ($characteristic->type == "string") {
                $this->prepareStringValues($characteristic);
            }
        }
        return [
            'boolean_value' => 'boolean|nullable',
            'number_value' => 'numeric|nullable|min:' . $this->intMin . '|max:' . $this->intMax,
            'string_value' => 'max:255' . $this->stringValues . '|nullable',
            'product_id' => 'required|exists:products,id',
            'characteristic_id' => 'required|exists:characteristics,id',
        ];
    }

    private function prepareNumberValues($characteristic)
    {
        $this->intMin = $characteristic->int_value_start;
        $this->intMax = $characteristic->int_value_end;
    }

    private function prepareStringValues($characteristic)
    {
        $this->stringValues = '|in:';
        foreach ($characteristic->characteristicValues as $value) {
            $this->stringValues .= $value->value . ',';
        }
        $this->stringValues = mb_substr($this->stringValues, 0, -1);
    }
}
