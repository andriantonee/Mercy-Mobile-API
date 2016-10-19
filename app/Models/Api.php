<?php

namespace App\Models;

use Hash;

class Api
{
    /*
     * All Create function here...
     */
    public static function create_members(array $data)
    {
        Member::create([
            'username'   => $data['username'],
            'password'   => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'address'    => $data['address'],
            'phone'      => $data['phone']
        ]);
    }
}