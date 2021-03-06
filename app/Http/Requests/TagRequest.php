<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        $id = $this->route()->parameters['tag'] ?? 0;
        return [
            'name' => 'required|unique:tags,name,' . $id . '|max:255'
        ];
    }

}
