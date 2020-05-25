<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = [
        'date', 'entity_type', 'entity_id', 'user_id'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
