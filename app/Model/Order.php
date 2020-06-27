<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table="tb_order";

    protected $guarded= [];

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class,"order_id","id");
    }


}
