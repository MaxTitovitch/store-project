<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharacteristicValue extends Model
{
    protected $fillable = [
        'value', 'characteristic_id'
    ];

    public function characteristic()
    {
        return $this->belongsTo('App\Characteristic');
    }
}
