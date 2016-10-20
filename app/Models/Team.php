<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $dates = [];
    protected $dateFormat = 'U';
    protected $fillable = ['games_id', 'username', 'name'];
    protected $hidden = [];

    public $incrementing = true;
    
    public $timestamps = true;

    public function game()
    {
        return $this->belongsTo('App\Models\Game', 'games_id', 'id');
    }

    public function team_name()
    {
        return $this->belongsTo('App\Models\TeamName', 'name', 'name');
    }

    public function leader()
    {
        return $this->belongsTo('App\Models\Member', 'username', 'username');
    }

    public function members()
    {
        return $this->belongsToMany('App\Models\Member', 'teams_details', 'teams_id', 'username')->withPivot('joined_at');
    }

    public function members_pendings()
    {
        return $this->belongsToMany('App\Models\Member', 'teams_details_pendings', 'teams_id', 'username')->withPivot('invited_at', 'requested_at');
    }
}