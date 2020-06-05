<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class MassUploadRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'file' => 'required|file',
            'type' => 'required|in:hard,soft',
        ];
    }
}
