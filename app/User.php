<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'sex', 'date_of_birth', 'email', 'phone', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function likes()
    {
        return $this->belongsToMany('App\Like');
    }

    public function views()
    {
        return $this->belongsToMany('App\View');
    }

    public function comments()
    {
        return $this->belongsToMany('App\Comment');
    }

    public function rankings()
    {
        return $this->belongsToMany('App\Ranking');
    }

    public function posts()
    {
        return $this->belongsToMany('App\Post');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }

    public function addresses()
    {
        return $this->belongsToMany('App\Address');
    }
}
