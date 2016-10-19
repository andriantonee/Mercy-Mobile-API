<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterMembersRequest extends FormRequest
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
            'username'   => 'required|alpha_dash|min:2|max:20|unique:members,username,NULL,username',
            'password'   => 'required|alpha_num|min:6|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'string|max:100',
            'email'      => 'required|email|max:255|unique:members,email,NULL,username',
            'address'    => 'required|string',
            'phone'      => 'required|string|regex:/(62)[0-9]{9,12}/'
        ];
    }
}