<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class TopRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'size' => 'required|integer|min:3|max:30',
        ];
    }
}
