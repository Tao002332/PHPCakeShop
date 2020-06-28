<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductSpu extends Model
{
    //
    protected $table="tb_product_spu";

    protected $guarded= ['skus'];

    public function skus() {
        return $this->hasMany(ProductSku::class,'spu_id','id');
    }

}
