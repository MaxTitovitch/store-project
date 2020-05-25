<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'percent', 'date_start', 'date_end', 'product_id', 'sale_category_id'
    ];

    public function saleCategory()
    {
        return $this->belongsTo('App\SaleCategory');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
