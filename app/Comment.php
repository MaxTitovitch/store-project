<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function entity()
    {
        return $this->morphTo();
    }

    public function likes()
    {
        return $this->morphMany('App\Like', 'entity');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
