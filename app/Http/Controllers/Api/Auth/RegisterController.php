<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Other;
use Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $rules = [
            'username' => 'required|alpha_dash|min:2|max:20|unique:members,username,NULL,username',
            'password' => 'required|alpha_num|min:6|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name' => 'string|max:100',
            'phone' => 'required|string|regex:/(62)[0-9]{9,12}/'
        ];

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        try {
            Member::create([
                'username' => $request->input('username'),
                'password' => Hash::make($request->input('password')),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name') ?: '',
                'phone' => $request->input('phone'),
                'status' => 0
            ]);
        } catch (\Exception $e) {
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(201, 'You have been registered successfully.');
    }
}