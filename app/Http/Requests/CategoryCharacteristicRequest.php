<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CategoryCharacteristicRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
//            'category_id' => 'required|exists:categories,id',
//            'characteristic_id' => 'required|exists:characteristics,id',
            'characteristics' => 'array'
        ];
    }
}
