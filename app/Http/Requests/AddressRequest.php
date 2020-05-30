<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'country' => 'required|max:255',
            'city' => 'required|max:255',
            'street' => 'required|max:255',
            'house' => 'required|max:255',
            'perch' => 'integer|max:50',
            'floor' => 'integer|max:100',
            'apartment' => 'required|max:255',
            'user_id' => 'required|exists:users,id'
        ];
    }
}
