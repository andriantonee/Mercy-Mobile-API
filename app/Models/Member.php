<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
// use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
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
    protected $dateFormat = 'U';
    protected $fillable = ['username', 'password', 'first_name', 'last_name', 'address', 'phone', 'email'];
    protected $hidden = ['password', 'remember_token'];

    public $incrementing = false;
    
    public $timestamps = true;

    public function findForPassport($username)
    {
        return self::where('username', $username)->first();
    }
}