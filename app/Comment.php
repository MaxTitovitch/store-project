<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'text', 'date', 'entity_type', 'entity_id', 'user_id'
    ];

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
