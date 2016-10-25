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
        Api::delete_teams_details_pendings_on_games($games_id, $user);
        $team = new Team([
            'games_id' => $data['games_id'],
            'username' => $user->username,
            'name' => $data['team_name']
        ]);
        $team->save();
        $team->members()->attach($user->username, ['joined_at' => time()]);
    }

    public static function create_teams_details_pendings_invite($teams_id, $username)
    {
        $team = Team::find($teams_id);
        $team->members_pendings()->attach($username, ['invited_at' => time()]);
    }

    public static function create_teams_details_pendings_join($teams_id, $username)
    {
        $team = Team::find($teams_id);
        $team->members_pendings()->attach($username, ['requested_at' => time()]);
    }

    public static function create_teams_details($teams_id, Member $member)
    {
        $team = Team::find($teams_id);
        Api::delete_teams_details_pendings_on_games($team->games_id, $member);
        $team->members()->attach($member, ['joined_at' => time()]);
    }

    /*
     * All Update function here...
     */
    public static function update_members(array $data, Member $member)
    {
        if (isset($data['password']))
        {
            $member->password = Hash::make($data['password']);
        }
        if (isset($data['first_name']))
        {
            $member->first_name = $data['first_name'];
        }
        if (isset($data['last_name']))
        {
            $member->last_name = $data['last_name'];
        }
        if (isset($data['address']))
        {
            $member->address = $data['address'];
        }
        if (isset($data['phone']))
        {
            $member->phone = $data['phone'];
        }
        $member->save();
    }

    /*
     * All Delete function here...
     */
    public static function delete_members_games($teams_id, Member $member)
    {
        $member->games()->detach($member->teams()->find($teams_id)->pluck('games_id'));
    }

    public static function delete_teams_details($teams_id, $username)
    {
        $member = Member::find($username);
        if ($member->teams()->detach($teams_id) == 0)
        {
            throw new \Exception('Pesan Member Tidak Bergabung');
        }
        Api::delete_members_games($teams_id, $member);
    }

    public static function delete_teams_details_pendings_on_games($games_id, Member $member)
    {
        $member->teams_pendings()->detach(Team::where('games_id', $games_id)->pluck('games_id'));
    }

    public static function delete_teams_details_pendings_on_teams($teams_id, Member $member)
    {
        $member->teams_pendings()->detach($teams_id);
    }
}