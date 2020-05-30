<?php

namespace App\Http\Requests;

use App\Characteristic;
use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductCharacteristicRequest extends FormRequest
{
    use ApiRequest;
    private $stringValues = '';
    private $intMax=0;
    private $intMin=10000;

    public function rules()
    {
        $characteristic = Characteristic::find($this->request->all()['characteristic_id']);
        if($characteristic != null) {
            if($characteristic->type == "number") {
                $this->prepareNumberValues($characteristic);
            } else if ($characteristic->type == "string") {
                $this->prepareStringValues($characteristic);
            }
        }
        return [
            'boolean_value' => 'boolean',
            'number_value' => 'numeric|min:' . $this->intMin . '|max:' . $this->intMax,
            'string_value' => 'max:255' . $this->stringValues,
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
