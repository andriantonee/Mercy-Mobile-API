<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Models\Api;
use App\Models\Game;
use DB;

class TeamController extends Controller
{
    public function create_team(TeamRequest $request)
    {
        if ($request->user()->teams()->where('games_id', $request->all()['games_id'])->get()->all())
        {
            return response()->json('Anda sudah memiliki team untuk kategori game '.Game::find($request->all()['games_id'])->name, 422);
        }

        DB::beginTransaction();
        try
        {
            Api::create_teams($request->all(), $request->user());
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Terjadi kesalahan pada server, silahkan coba kembali.', 500);
        }

        return response()->json('Team telah berhasil dibuat!', 200);
    }
}