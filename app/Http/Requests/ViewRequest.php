<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class ViewRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        $type = $this->request->all()['entity_type'] . 's';
        return [
            'date' => 'required|date_equals:today',
            'entity_type' => 'required|in:post,product',
            'entity_id' => 'required|exists:' .$type .',id',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
