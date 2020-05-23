<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public function saleCategory()
    {
        return $this->belongsTo('App\SaleCategory');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
