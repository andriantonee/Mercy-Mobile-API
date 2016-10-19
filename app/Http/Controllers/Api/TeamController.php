<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api;
use DB;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function create_team(Request $request)
    {
        DB::beginTransaction();
        try
        {
            Api::create_teams($request->all(), $request->user());
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json('Something went wrong, Please Try Again!', 500);
        }

        return response()->json('Team has been created successfully!', 200);
    }
}