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
        return $this->belongsToMany('App\Order', 'product_order');
    }
}
