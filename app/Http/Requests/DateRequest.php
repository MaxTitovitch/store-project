<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class DateRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'quantity' => 'integer|min:3|max:10'
        ];
    }
}
