<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PropertyKey extends Model
{
    //
    protected $table="tb_property_key";

    protected $guarded= [];

    public function values() {
        return $this->hasMany(PropertyValue::class,'pv_id','id');
    }

}
