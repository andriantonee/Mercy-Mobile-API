<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Api;
use App\Models\Game;
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

    public function invite_members_to_team($teams_id, Request $request)
    {
        DB::beginTransaction();
        try
        {
            Api::create_teams_details_pendings($teams_id, $request->all()['members']);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Beberapa Member sudah pernah di invite, silahkan coba kembali.', 422);
        }

        DB::commit();
        return response()->json('Member telah berhasil di invite!', 200);
    }
}