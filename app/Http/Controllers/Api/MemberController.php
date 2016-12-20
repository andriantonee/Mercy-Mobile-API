<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Other;
use App\Models\Team;
use DB;
use Hash;
use Illuminate\Http\Request;
use Validator;

class MemberController extends Controller
{
    public function update_profile(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'string|max:100',
            'phone' => 'required|string|regex:/(62)[0-9]{9,12}/'
        ];

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        try {
            $profile = $request->user();
            $profile->first_name = $request->input('first_name');
            $profile->last_name = $request->input('last_name');
            $profile->phone = $request->input('phone');
            $profile->save();
        } catch (\Exception $e) {
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, 'Profile has been updated successfully.', [
            'users' => [
                'username' => $profile->username,
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'phone' => $profile->phone,
                'teams_id' => $profile->teams_id,
                'status' => $profile->status
            ]
        ]);
    }

    public function update_password(Request $request)
    {
        $rules = [
            'old_password' => 'required|alpha_num',
            'password' => 'required|alpha_num|min:6|confirmed'
        ];

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        if (!Hash::check($request->input('old_password'), $request->user()->password)) {
            return Other::response_json(400, 'Old password is incorrect.');
        }

        try {
            $password = $request->user();
            $password->password = Hash::make($request->input('password'));
            $password->save();
        } catch (\Exception $e) {
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, 'Password has been changed successfully.');
    }

    public function get_team_invitation(Request $request)
    {
        $team_invitations = $request->user()->team_invitations()
            ->withPivot('invited_at')
            ->with([
                'leader' => function ($leader) {
                    $leader->select('members.teams_id', 'members.username', 'members.first_name', 'members.last_name');
                }
            ])
            ->orderBy('teams_invitations.invited_at', 'desc')
            ->get();

        $response_array = [];
        foreach ($team_invitations as $team) {
            $response_array['teams'][] = [
                'id' => $team->id,
                'team_name' => $team->name,
                'leader_username' => $team->leader[0]->username,
                'leader_name' => $team->leader[0]->first_name . ($team->leader[0]->last_name ? ' ' . $team->leader[0]->last_name : ''),
                'created_at' => strtotime($team->created_at),
                'invite_at' => strtotime($team->pivot->invited_at)
            ];
        }

        return Other::response_json(200, 'Get team invitations success.', $response_array);
    }

    public function accept_team_invitation(Request $request)
    {
        $rules = [
            'teams_id' => 'required|integer|min:1|exists:teams,id'
        ];

        if (!Team::find($request->input('teams_id'))) {
            return Other::response_json(400, 'The selected teams id is invalid.');
        }

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        if (!$request->user()->team_invitations()->find($request->input('teams_id'))) {
            return Other::response_json(400, 'User do not have any invitation from that team.');
        }

        if ($request->user()->teams_id) {
            return Other::response_json(400, 'User already has a team.');
        }

        DB::beginTransaction();
        try {
            $user = $request->user();
            $user->teams_id = $request->input('teams_id');
            $user->status = 1;
            $user->save();

            $user->team_invitations()->detach($request->input('teams_id'));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, 'User has joined the team.');
    }

    public function reject_team_invitation(Request $request)
    {
        $rules = [
            'teams_id' => 'required|integer|min:1|exists:teams,id'
        ];

        if (!Team::find($request->input('teams_id'))) {
            return Other::response_json(400, 'The selected teams id is invalid.');
        }

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        if (!$request->user()->team_invitations()->find($request->input('teams_id'))) {
            return Other::response_json(400, 'User do not have any invitation from that team.');
        }

        try {
            $user = $request->user();
            $user->team_invitations()->detach($request->input('teams_id'));
        } catch (\Exception $e) {
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, 'User has cancelled the team invitation request.');
    }
}