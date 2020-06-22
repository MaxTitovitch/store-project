<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'price', 'description', 'ranking', 'category_id'
    ];

    public function comments()
    {
        return $this->morphMany('App\Comment', 'entity');
    }

    public function views()
    {
        return $this->morphMany('App\View', 'entity');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function sales()
    {
        return $this->hasMany('App\Sale');
    }

    public function product_characteristics()
    {
        return $this->hasMany('App\ProductCharacteristic');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
