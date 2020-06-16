<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:jpeg,gif,png',
            'slug' => 'required|string|max:255',
            'delete-slug' => 'string|max:255',
            'type' => 'required|in:posts,users,products',
        ];
    }
}
