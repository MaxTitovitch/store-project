<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'country', 'city', 'street', 'house', 'perch', 'floor', 'apartment', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
