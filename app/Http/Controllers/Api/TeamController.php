<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Http\Requests\TeamInviteRequest;
use App\Models\Api;
use App\Models\Game;
use App\Models\Member;
use App\Models\Team;
use DB;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function create_team(TeamRequest $request)
    {
        $request_data = $request->all();

        try
        {
            Api::create_teams_names($request_data['team_name']);
        }
        catch(\Exception $e)
        {
            // Just Continue
        }
        DB::beginTransaction();
        try
        {
            Api::create_members_games($request_data['games_id'], $request->user());
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Anda sudah memiliki team untuk kategori game '.Game::find($request_data['games_id'])->name, 422);
        }
        try
        {
            Api::create_teams_names_games($request_data['games_id'], $request_data['team_name']);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Maaf, nama team ini telah dipakai.', 422);
        }
        try
        {
            Api::create_teams($request_data, $request->user());
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Terjadi kesalahan pada server, silahkan coba kembali.', 500);
        }
        DB::commit();

        return response()->json('Team telah berhasil dibuat!', 200);
    }

    public function invite_member_to_team($teams_id, $username, Request $request)
    {
        $team = Team::find($teams_id);
        $member = Member::find($username);

        if (!$team)
        {
            return response()->json('Team tidak ditemukan!', 422);
        }
        if ($team->username != $request->user()->username)
        {
            return response()->json('Anda bukan ketua dalam team tersebut!', 422);
        }
        if (!$member)
        {
            return response()->json('Member tidak ditemukan!', 422);
        }

        DB::beginTransaction();
        try
        {
            Api::create_teams_details_pendings($teams_id, $username);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Member tersebut sudah pernah di invite!', 422);
        }
        DB::commit();

        return response()->json('Member telah berhasil di invite!', 200);
    }

    public function accept_team_invitation($teams_id, Request $request)
    {
        $team = Team::find($teams_id);

        if (!$team)
        {
            return response()->json('Team tidak ditemukan!', 422);
        }
        if (!$team->members_pendings()->where('teams_details_pendings.username', $request->user()->username)->whereNotNull('invited_at')->exists())
        {
            return response()->json('Team tersebut tidak menginvite anda ke dalam teamnya!', 422);
        }

        DB::beginTransaction();
        try
        {
            Api::create_members_games($team->games_id, $request->user());
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Anda sudah memiliki team untuk kategori game '.Game::find($request_data['games_id'])->name, 422);
        }
        try
        {
            Api::create_teams_details($teams_id, $request->user());
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Terjadi kesalahan pada server, silahkan coba kembali.', 500);
        }
        DB::commit();

        return response()->json('Anda telah resmi menjadi anggota team tersebut!', 200);
    }

    public function kick_team_member($teams_id, $username, Request $request)
    {
        $team = Team::find($teams_id);

        if (!$team)
        {
            return response()->json('Team tidak ditemukan!', 422);
        }
        if ($team->username != $request->user()->username)
        {
            return response()->json('Anda bukan ketua dalam team tersebut!', 422);
        }
        if (!$team->members()->find($username))
        {
            return response()->json('Member tersebut tidak bergabung dalam team ini!', 422);
        }
        if ($username == $request->user()->username)
        {
            return response()->json('Tidak dapat mengeluarkan diri anda sendiri dari team ini!', 422);
        }

        DB::beginTransaction()
        try
        {
            Api::delete_teams_details($teams_id, $username);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return response()->json('Terjadi kesalahan pada server, silahkan coba kembali.', 500);
        }

        return response()->json('Anda telah berhasil mengeluarkan user tersebut dalam team!', 200);
    }
}