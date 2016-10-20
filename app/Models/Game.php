<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';

    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $dates = [];
    protected $dateFormat = 'U';
    protected $fillable = ['name'];
    protected $hidden = [];

    public $incrementing = true;
    
    public $timestamps = true;

    public function members()
    {
        return $this->belongsToMany('App\Models\Member', 'members_games', 'games_id', 'username');
    }

    public function teams()
    {
        return $this->hasMany('App\Models\Team', 'games_id', 'id');
    }

    public function team_names()
    {
        return $this->belongsToMany('App\Models\TeamName', 'teams_names_games', 'games_id', 'teams_names_name')->withTimestamps();
    }
}