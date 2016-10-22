<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMemberPasswordRequest;
use App\Http\Requests\EditMemberProfileRequest;
use App\Models\Api;
use DB;
use Hash;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function update_profile(EditMemberProfileRequest $request)
    {
        $data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone')
        ];

        try
        {
            Api::update_members($data, $request->user());
        }
        catch(\Exception $e)
        {
            return response()->json('Terjadi kesalahan pada server, silahkan coba kembali.', 500);
        }

        return response()->json('Berhasil mengupdate profil!', 200);
    }

    public function change_password(ChangeMemberPasswordRequest $request)
    {
        $data = [
            'password' => $request->input('password')
        ];

        if (!Hash::check($request->input('old_password'), $request->user()->password))
        {
            return response()->json('Password lama anda salah!', 422);
        }

        try
        {
            Api::update_members($data, $request->user());
        }
        catch(\Exception $e)
        {
            return response()->json('Terjadi kesalahan pada server, silahkan coba kembali.', 500);
        }

        return response()->json('Berhasil mengubah password!', 200);
    }
}