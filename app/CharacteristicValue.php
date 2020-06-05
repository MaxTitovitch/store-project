<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharacteristicValue extends Model
{
    protected $fillable = [
        'value', 'characteristic_id'
    ];

    public static function createOrUpdate ($value, $characteristic) {
        $characteristicValue = self::where('value', $value)->where('characteristic_id', $characteristic->id)->first();

        if($characteristicValue == null) {
            $characteristicValue = new CharacteristicValue();
            $characteristicValue->value = $value;
            $characteristicValue->characteristic_id = $characteristic->id;
            $characteristicValue->save();
        }
        return $characteristicValue;
    }

    public function characteristic()
    {
        return $this->belongsTo('App\Characteristic');
    }
}
