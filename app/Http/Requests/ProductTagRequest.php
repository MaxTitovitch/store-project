<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductTagRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
//            'product_id' => 'required|exists:products,id',
//            'tag_id' => 'required|exists:tags,id',
            'tags' => 'array'
        ];
    }
}
