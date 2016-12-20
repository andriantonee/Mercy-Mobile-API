<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    protected $table = 'teams';

    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $dates = ['created_at', 'deleted_at'];
    protected $fillable = ['name', 'created_at'];
    protected $hidden = [];

    public $incrementing = true;

    public $timestamps = false;

    public function scopeId($query, $id)
    {
        return $query->where('teams.id', $id);
    }

    public function scopeNotid($query, $id)
    {
        return $query->where('teams.id', '!=', $id);
    }

    public function scopeName($query, $name)
    {
        return $query->where('teams.name', $name);
    }

    public function members()
    {
        return $this->hasMany('App\Models\Member', 'teams_id', 'id')->member();
    }

    public function leader()
    {
        return $this->hasMany('App\Models\Member', 'teams_id', 'id')->leader();
    }

    public function invite_list()
    {
        return $this->belongsToMany('App\Models\Member', 'teams_invitations', 'teams_id', 'username');
    }

    public function following_tournaments()
    {
        return $this->belongsToMany('App\Models\Tournament', 'tournament_participants', 'teams_id', 'tournament_id');
    }
}