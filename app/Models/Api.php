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
        $member = new Member([
            'username'   => $data['username'],
            'password'   => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'address'    => $data['address'],
            'phone'      => $data['phone']
        ]);

        $member->save();
    }

    public static function create_teams(array $data, $authenticate_user)
    {
        $team = new Team([
            'games_id' => $data['games_id'],
            'members_username' => $authenticate_user->username,
            'name' => $data['team_name']
        ]);

        $team->save();

        $team->members()->attach($authenticate_user, ['joined_at' => time()]);
    }
}