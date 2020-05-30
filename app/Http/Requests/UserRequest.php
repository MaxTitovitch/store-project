<?php

namespace App\Http\Requests;

use App\Http\Requests\API\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    use ApiRequest;

    public function rules()
    {
        $id = $this->route()->parameters['tag'] ?? 0;
        return [
            'name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'sex' => 'required|in:Мужчина,Женщина',
            'date_of_birth' => 'required|date|after:01-01-1900|before:today|date_format:Y-m-d',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|min:100000000|numeric|unique:users,phone,' . $id,
            'password' => 'required|max:255',
            'confirm_password' => 'required|same:password',
        ];
    }
}
