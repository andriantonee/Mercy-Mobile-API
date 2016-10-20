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
            'last_name'  => isset($data['last_name']) ? $data['last_name'] : '',
            'email'      => $data['email'],
            'address'    => $data['address'],
            'phone'      => $data['phone']
        ]);

        $member->save();
    }

    public static function create_members_games($games_id, Member $user)
    {
        $user->games()->attach($games_id);
    }

    public static function create_teams_names($team_name)
    {
        $teams_names = new TeamName([
            'name' => $team_name
        ]);

        $teams_names->save();
    }

    public static function create_teams_names_games($games_id, $team_name)
    {
        TeamName::find($team_name)->games()->attach($games_id, ['created_at' => time(), 'updated_at' => time()]);
    }

    public static function create_teams(array $data, Member $user)
    {
        $team = new Team([
            'games_id' => $data['games_id'],
            'username' => $user->username,
            'name' => $data['team_name']
        ]);

        $team->save();

        $team->members()->attach($user->username, ['joined_at' => time()]);
    }

    public static function create_teams_details_pendings($teams_id, array $members)
    {
        $team = Team::find($teams_id);
        foreach($members as $member)
        {
            $team->members_pendings()->attach($member, ['invited_at' => time()]);
        }
    }
}