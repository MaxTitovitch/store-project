<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|size:255',
            'last_name' => 'required|size:255',
            'sex' => 'required|in:Мужчина,Женщина',
            'date_of_birth' => 'required|date|after:01-01-1900|before:today|date_format:Y-m-d',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:100000000|numeric|unique:users',
            'password' => 'required|size:255',
            'confirm_password' => 'required|same:password',
        ];
    }
}
