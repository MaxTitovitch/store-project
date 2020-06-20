<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'date' => 'required|date',
            'delivery_date' => 'required|after_or_equal:date',
            'status' => 'required|in:В обработке,Выполнен,Отменён|max:255',
            'comment' => 'max:2000',
            'user_id' => 'exists:users,id',
            'address_id' => 'exists:addresses,id',
        ];



    }
}
