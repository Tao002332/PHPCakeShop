<?php

namespace App\Http\Controllers\Api\Utils;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CommonController;

class CSRFController extends ApiController
{
    //
    public function getCsrf() {
        return $this->success("csrf成功",csrf_token());
    }

}
