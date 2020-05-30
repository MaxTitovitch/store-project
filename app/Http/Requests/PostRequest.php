<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'text' => 'required|max:20000',
            'date' => 'required|date_equals:today',
            'user_id' => 'required|exists:users,id',
            'top_id' => 'exists:tops,id',
        ];



    }
}
