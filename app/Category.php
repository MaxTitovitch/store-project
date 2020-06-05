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

    public static function findOrCreate($name){
        $category = self::where('name', $name)->first();
        return $category ?? false;
    }

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
