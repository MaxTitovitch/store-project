<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductTopRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'top_id' => 'required|exists:tops,id',
        ];
    }
}
