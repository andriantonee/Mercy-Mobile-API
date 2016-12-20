<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('games')->where('id', 1)->exists()) {
            DB::table('games')->insert([
                'id' => 1,
                'name' => 'Dota 2',
                'image_url' => '/img/games/dota_2.jpg',
                'created_at' => Carbon::now()
            ]);
        }
        if (!DB::table('games')->where('id', 2)->exists()) {
            DB::table('games')->insert([
                'id' => 2,
                'name' => 'League of Legends',
                'image_url' => '/img/games/league_of_legends.jpg',
                'created_at' => Carbon::now()
            ]);
        }
        if (!DB::table('games')->where('id', 3)->exists()) {
            DB::table('games')->insert([
                'id' => 3,
                'name' => 'CS:GO',
                'image_url' => '/img/games/cs_go.jpg',
                'created_at' => Carbon::now()
            ]);
        }
        if (!DB::table('games')->where('id', 4)->exists()) {
            DB::table('games')->insert([
                'id' => 4,
                'name' => 'Overwatch',
                'image_url' => '/img/games/overwatch.jpg',
                'created_at' => Carbon::now()
            ]);
        }
    }
}
