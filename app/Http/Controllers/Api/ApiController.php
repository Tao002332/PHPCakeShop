<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ApiResponseTraits;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast\Object_;

class ApiController extends Controller
{
    //
    use ApiResponseTraits;

    /**
     * 动态更新
     * @param $data
     * @param $filed
     * @param Model $obj
     * @return Model
     */
    public function dynamicUpdate($data,$filed,Model $obj) {
        foreach ($filed as $v) {
            if(isset($data[$v])) {
                $obj[$v]=$data[$v];
            }
        }
        return $obj;
    }

}
