<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamName extends Model
{
    protected $table = 'teams_names';

    protected $primaryKey = 'name';
    protected $keyType = 'string';

    protected $dates = [];
    protected $dateFormat = 'U';
    protected $fillable = ['name'];
    protected $hidden = [];

    public $incrementing = false;
    
    public $timestamps = true;

    public function teams()
    {
        return $this->hasMany('App\Models\Team', 'name', 'name');
    }

    public function games()
    {
        return $this->belongsToMany('App\Models\Game', 'teams_names_games', 'teams_names_name', 'games_id')->withTimestamps();
    }
}