<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'parent_id'
    ];

    public function parentCategory()
    {
        return $this->belongsTo('App\Category', 'parent_id');
    }

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function characteristics()
    {
        return $this->belongsToMany('App\Characteristic');
    }
}
