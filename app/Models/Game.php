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

    public function teams()
    {
        return $this->hasMany('App\Models\Team', 'games_id', 'id');
    }
}