<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{
    protected $table = 'oauth_clients';

    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $dates = [];
    protected $fillable = [];
    protected $hidden = ['secret'];

    public $incrementing = true;

    public $timestamps = true;

    public function scopeId($query, $id)
    {
        return $query->where('id', $id);
    }
}
