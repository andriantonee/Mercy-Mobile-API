<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $table = 'tournaments';

    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $dates = ['registration_open', 'registration_close'];
    protected $fillable = ['name', 'games_id', 'registration_open', 'registration_close', 'member_participant_in_one_team', 'location', 'description', 'rules'];
    protected $hidden = [];

    public $incrementing = false;

    public $timestamps = true;

    public function scopeLike_name($query, $name)
    {
        return $query->where('tournaments.name', 'like', '%' . $name . '%');
    }

    public function scopeGame_category($query, $game_category)
    {
        return $query->where('tournaments.games_id', $game_category);
    }

    public function scopeBetween_open_date_and_close_date($query, $date)
    {
        return $query->where('tournaments.registration_open', '<=', $date)
            ->where('tournaments.registration_close', '>=', $date);
    }

    public function game()
    {
        return $this->belongsTo('App\Models\Game', 'games_id', 'id');
    }

    public function participant()
    {
        return $this->belongsToMany('App\Models\Team', 'tournament_participants', 'tournament_id', 'teams_id');
    }
}
