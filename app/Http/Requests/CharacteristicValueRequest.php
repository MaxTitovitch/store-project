<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CharacteristicValueRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'value' => 'required|max:255',
            'characteristic_id' => 'required|exists:characteristics,id'
        ];
    }
}
