<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //
    protected $table="tb_order_detail";

    protected $guarded= ['spu_id','discount','origin_price'];

}
