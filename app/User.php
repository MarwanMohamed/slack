<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    

    public function tokens()
    {
        return $this->hasMany(UserToken::class)->latest();
    }

    public function getLatestTokenAttribute()
    {
        if($this->tokens()->count() == 0){
           return false;
        }
        
        return $this->tokens()->first();

    }
}
