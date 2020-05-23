<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
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
