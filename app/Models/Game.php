<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;

    protected $table = 'games';

    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $dates = ['created_at', 'deleted_at'];
    protected $fillable = ['name', 'created_at'];
    protected $hidden = [];

    public $incrementing = true;

    public $timestamps = false;

    public function tournaments()
    {
        return $this->hasMany('App\Models\Tournament', 'games_id', 'id');
    }
}