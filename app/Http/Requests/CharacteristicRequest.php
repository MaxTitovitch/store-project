<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class CharacteristicRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'type' => 'required|in:boolean,number,string',
            'int_value_start' => 'required_if:type,number|numeric|nullable',
            'int_value_end' => 'required_if:type,number|numeric|required_with:int_value_start|gt:int_value_start|nullable',
        ];
    }
}
