<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'percent' => 'required|integer|min:1|max:100',
            'date_start' => 'required',
            'date_end' => 'required|after_or_equal:date_start',
            'product_id' => 'required|exists:products,id',
            'sale_category_id' => 'required|exists:sale_categories,id',
        ];
    }
}
