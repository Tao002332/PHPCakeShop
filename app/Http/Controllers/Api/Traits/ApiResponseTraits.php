<?php

namespace App\Http\Controllers\Api\Traits;

use Response;
use App\Enums\ResponseCode;

trait ApiResponseTraits
{

    public function success($message,$data=[]) {
        return response()->json([
            'code'=>ResponseCode::OK,
            'flag'=>true,
            'message'=>$message==null?config('errorcode.code')[ResponseCode::OK]:$message,
            'data'=>$data,
        ]);
    }

    public function fail($code,$message=null,$data=[]) {
        return response()->json([
            'code'=>$code,
            'flag'=>false,
            'message'=>$message==null?config('errorcode.code')[(int)$code]:$message,
            'data'=>$data,
        ]);
    }


}
