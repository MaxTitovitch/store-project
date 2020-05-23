<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleCategory extends Model
{
    public function sales()
    {
        return $this->hasMany('App\Sale');
    }
}
