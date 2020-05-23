<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharacteristicValue extends Model
{
    public function characteristic()
    {
        return $this->belongsTo('App\Characteristic');
    }
}
