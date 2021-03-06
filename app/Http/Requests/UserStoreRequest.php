<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'name' => 'required|min:3|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|min:3|unique:users,username',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'status' => 'required'
        ];
    }
}
