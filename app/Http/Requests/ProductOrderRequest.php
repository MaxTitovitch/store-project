<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProductOrderRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
//            'product_id' => 'required|exists:products,id',
//            'order_id' => 'required|exists:orders,id',
            'products' => 'array'
        ];
    }
}
