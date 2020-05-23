<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Top extends Model
{
    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }
}
