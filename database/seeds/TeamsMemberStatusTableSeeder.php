<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class TeamsMemberStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('teams_member_status')->where('id', 0)->exists()) {
            DB::table('teams_member_status')->insert([
                'id' => 0,
                'name' => 'None',
                'created_at' => Carbon::now()
            ]);
        }
        if (!DB::table('teams_member_status')->where('id', 1)->exists()) {
            DB::table('teams_member_status')->insert([
                'id' => 1,
                'name' => 'Member',
                'created_at' => Carbon::now()
            ]);
        }
        if (!DB::table('teams_member_status')->where('id', 2)->exists()) {
            DB::table('teams_member_status')->insert([
                'id' => 2,
                'name' => 'Leader',
                'created_at' => Carbon::now()
            ]);
        }
    }
}
