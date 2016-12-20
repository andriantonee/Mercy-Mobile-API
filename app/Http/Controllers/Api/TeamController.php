<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamInviteRequest;
use App\Models\Member;
use App\Models\Other;
use App\Models\Team;
use Carbon;
use DB;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function get_team_detail(Request $request)
    {
        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        $team_detail = Team::with([
            'leader' => function ($leader) {
                $leader->select('members.teams_id', 'members.username', 'members.first_name', 'members.last_name');
            }
        ])
            ->select('teams.id', 'teams.name', 'teams.created_at')
            ->find($teams_id);

        $response_array = [
            'isLeader' => $request->user()->status == 2,
            'team' => [
                'id' => $team_detail->id,
                'team_name' => $team_detail->name,
                'leader_username' => $team_detail->leader[0]->username,
                'leader_name' => $team_detail->leader[0]->first_name . ($team_detail->leader[0]->last_name ? ' ' . $team_detail->leader[0]->last_name : ''),
                'created_at' => strtotime($team_detail->created_at)
            ]
        ];

        return Other::response_json('200', 'User has a team.', $response_array);
    }

    public function get_team_member(Request $request)
    {
        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        $team_detail = Team::with([
            'members' => function ($members) {
                $members->select('members.teams_id', 'members.username', 'members.first_name', 'members.last_name');
            }
        ])
            ->select('teams.id', 'teams.name', 'teams.created_at')
            ->find($teams_id);

        $response_array = [
            'isLeader' => $request->user()->status == 2,
            'teams_id' => $teams_id
        ];

        foreach ($team_detail->members as $member) {
            $response_array['members'][] = [
                'username' => $member->username,
                'name' => $member->first_name . ($member->last_name ? ' ' . $member->last_name : '')
            ];
        }

        return Other::response_json('200', 'User has a team.', $response_array);
    }

    public function get_team_member_pending_invitations(Request $request)
    {
        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        if (!($leader = $request->user()->status == 2)) {
            return Other::response_json(403, 'User is not a leader of the team.');
        }

        $pending_invitations = Team::with([
            'invite_list' => function ($invite_list) {
                $invite_list->withPivot('invited_at')
                    ->select('members.username', 'members.first_name', 'members.last_name')
                    ->orderBy('teams_invitations.invited_at', 'desc');
            }
        ])
            ->select('teams.id')
            ->find($teams_id);

        $response_array = [];
        foreach ($pending_invitations->invite_list as $member) {
            $response_array['members'][] = [
                'username' => $member->username,
                'name' => $member->first_name . ($member->last_name ? ' ' . $member->last_name : ''),
                'isInvited' => true,
                'invited_at' => strtotime($member->pivot->invited_at)
            ];
        }

        return Other::response_json('200', 'Get member pending invitations list success.', $response_array);
    }

    public function create_team(Request $request)
    {
        $rules = [
            'team_name' => 'required|string|max:50'
        ];

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        if ($request->user()->teams_id) {
            return Other::response_json(400, 'User already has a team.');
        }

        if (Team::name($request->input('team_name'))->exists()) {
            return Other::response_json(400, 'Team name not available.');
        }

        DB::beginTransaction();
        try {
            $team = new Team([
                'name' => $request->input('team_name'),
                'created_at' => Carbon::now()
            ]);
            $team->save();

            $user = $request->user();
            $user->teams_id = $team->id;
            $user->status = 2;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, 'Team has been created.');
    }

    public function update_team_detail(Request $request)
    {
        $rules = [
            'team_name' => 'required|string|max:50'
        ];

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        if (!($request->user()->status == 2)) {
            return Other::response_json(403, 'User is not a leader of the team.');
        }

        if (Team::name($request->input('team_name'))->notid($teams_id)->exists()) {
            return Other::response_json(400, 'Team name not available.');
        }

        try {
            Team::id($teams_id)
                ->update([
                    'name' => $request->input('team_name')
                ]);
        } catch (\Exception $e) {
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, 'Team name has been updated.');
    }

    public function disband_team(Request $request)
    {
        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        if ($leader = $request->user()->status == 2) {
            if ($request->user()->team->members->toArray()) {
                return Other::response_json(400, 'User is a leader of a team. So user must kick all the member first or let them out first.');
            }
        }

        DB::beginTransaction();
        try {
            $user = $request->user();
            $user->teams_id = null;
            $user->status = 0;
            $user->save();

            if ($leader) {
                Team::find($teams_id)->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, 'Team has been disband successfully.');
    }

    public function search_member(Request $request)
    {
        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        if (!($leader = $request->user()->status == 2)) {
            return Other::response_json(403, 'User is not a leader of the team.');
        }

        $search_result = Member::with([
            'team_invitations' => function ($team_invitations) use ($teams_id) {
                $team_invitations->withPivot('invited_at')
                    ->id($teams_id);
            }
        ])
            ->like_name_or_username((string)$request->input('keyword'))
            ->non_member()
            ->select('members.username', 'members.first_name', 'members.last_name')
            ->get();

        $response_array = [];
        foreach ($search_result as $member) {
            $response_array['members'][] = [
                'username' => $member->username,
                'name' => $member->first_name . ($member->last_name ? ' ' . $member->last_name : ''),
                'isInvited' => $member->team_invitations->toArray() != [],
                'invited_at' => $member->team_invitations->toArray() ? strtotime($member->team_invitations[0]->pivot->invited_at) : 0
            ];
        }

        return Other::response_json(200, 'Search members success.', $response_array);
    }

    public function invite_member(Request $request)
    {
        $rules = [
            'username' => 'required|alpha_dash|exists:members,username'
        ];

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        if (!($leader = $request->user()->status == 2)) {
            return Other::response_json(403, 'User is not a leader of the team.');
        }

        if (Member::find($request->input('username'))->teams_id) {
            return Other::response_json(400, $request->input('username') . ' already has a team.');
        }

        try {
            $request->user()->team->invite_list()->attach($request->input('username'), [
                'invited_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {
            return Other::response_json(400, $request->input('username') . ' is already in invite pending list.');
        }

        return Other::response_json(200, $request->input('username') . ' has been invited to the team.');
    }

    public function cancel_invite_member(Request $request)
    {
        $rules = [
            'username' => 'required|alpha_dash|exists:members,username'
        ];

        if ($res = Other::validate($request->all(), $rules)) {
            return $res;
        }

        if (!$teams_id = $request->user()->teams_id) {
            return Other::response_json(404, 'User does not has a team.');
        }

        if (!($leader = $request->user()->status == 2)) {
            return Other::response_json(403, 'User is not a leader of the team.');
        }

        if (!$request->user()->team->invite_list()->find($request->input('username'))) {
            return Other::response_json(400, $request->input('username') . ' not in invite pending list.');
        }

        try {
            $request->user()->team->invite_list()->detach($request->input('username'));
        } catch (\Exception $e) {
            return Other::response_json(500, 'Something went wrong. Please try again.');
        }

        return Other::response_json(200, $request->input('username') . ' has been cancelled from invite pending list.');
    }
}