<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'date', 'text', 'user_id', 'top_id'
    ];

    public function comments()
    {
        return $this->morphMany('App\Comment', 'entity');
    }

    public function views()
    {
        return $this->morphMany('App\View', 'entity');
    }

    public function likes()
    {
        return $this->morphMany('App\Like', 'entity');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function top()
    {
        return $this->belongsTo('App\Top');
    }
}
