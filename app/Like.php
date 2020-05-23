<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public function entity()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
