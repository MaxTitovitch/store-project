<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Top extends Model
{

    protected $fillable = [
        'name', 'size'
    ];

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product')->withPivot(['id', 'top']);
    }
}
