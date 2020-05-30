<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class SaleCategoryRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'required|max:20000',
        ];
    }
}
