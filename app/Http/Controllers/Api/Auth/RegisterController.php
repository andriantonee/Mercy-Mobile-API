<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterMembersRequest;
use App\Models\Api;

class RegisterController extends Controller
{
    public function Register(RegisterMembersRequest $request)
    {
        try
        {
            Api::create_members($request->all());
        }
        catch(\Exception $e)
        {
            return response()->json('Something went wrong, Please Try Again!', 500);
        }

        return response()->json('Registered successfully!', 200);
    }
}