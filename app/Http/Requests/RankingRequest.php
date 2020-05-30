<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class RankingRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'point' => 'required|in:1,2,3,4,5',
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ];
    }
}
