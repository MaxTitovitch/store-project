<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description'
    ];

    public function sales()
    {
        return $this->hasMany('App\Sale');
    }
}
