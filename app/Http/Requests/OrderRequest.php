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
            'date' => 'required|date_equals:today',
            'delivery_date' => 'required|after_or_equal:today',
            'status' => 'required|in:В обработке,Выполнен,Отменён|max:255',
            'comment' => 'required|max:2000',
            'user_id' => 'exists:users,id',
            'address_id' => 'exists:addresses,id',
        ];



    }
}
