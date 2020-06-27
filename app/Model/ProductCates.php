<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductCates extends Model
{
    //
    protected $table="tb_product_cates";

    protected $guarded= [];

    public function keys() {
        return $this->hasMany(PropertyKey::class,'cate_id','id');
    }

    public function spus() {
        return $this->hasMany(ProductSpu::class,'cate_id','id');
    }
}
