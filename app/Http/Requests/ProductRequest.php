<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        $id = $this->route()->parameters['product'] ?? 0;
        return [
            'name' => 'required|unique:products,name,' . $id . '|max:255',
            'price' => 'required|min:1|max:10000',
            'description' => 'required|max:2000',
            'ranking' => 'min:1|max:5',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
