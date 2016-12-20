<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Member extends Model implements
    AuthenticatableContract
{
    use Authenticatable, HasApiTokens, Notifiable;

    protected $table = 'members';

    protected $primaryKey = 'username';
    protected $keyType = 'string';

    protected $dates = [];
    protected $fillable = ['username', 'password', 'first_name', 'last_name', 'phone', 'status'];
    protected $hidden = ['password'];

    public $incrementing = false;

    public $timestamps = true;

    public function findForPassport($username)
    {
        return self::where('members.username', $username)->first();
    }

    public function scopeNon_member($query)
    {
        return $query->where('members.status', 0);
    }

    public function scopeMember($query)
    {
        return $query->where('members.status', 1);
    }

    public function scopeLeader($query)
    {
        return $query->where('members.status', 2);
    }

    public function scopeLike_name_or_username($query, $keyword)
    {
        return $query->where(function ($search) use ($keyword) {
            $search->whereRaw('TRIM(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $keyword . '%'])
                ->orWhere('members.username', 'like', '%' . $keyword . '%');
        });
    }

    public function scopeNot_in_list_invite($query, $teams_id)
    {
        return $query->whereNotIn('members.username', function ($pending_list) use ($teams_id) {
            $pending_list->select('teams_invitations.username')
                ->from('teams_invitations')
                ->where('teams_invitations.teams_id', $teams_id);
        });
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'teams_id', 'id');
    }

    public function team_invitations()
    {
        return $this->belongsToMany('App\Models\Team', 'teams_invitations', 'username', 'teams_id');
    }
}