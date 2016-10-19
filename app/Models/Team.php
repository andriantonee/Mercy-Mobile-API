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
    protected $fillable = ['games_id', 'members_username', 'name'];
    protected $hidden = [];

    public $incrementing = true;
    
    public $timestamps = true;

    public function game()
    {
        return $this->belongsTo('App\Models\Game', 'games_id', 'id');
    }

    public function leader()
    {
        return $this->belongsTo('App\Models\Member', 'members_username', 'username');
    }

    public function members()
    {
        return $this->belongsToMany('App\Models\Member', 'teams_details', 'teams_id', 'members_username');
    }
}