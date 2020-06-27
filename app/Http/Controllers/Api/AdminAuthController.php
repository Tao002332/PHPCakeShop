<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAuthController extends ApiController
{
    //

    public function __construct() {
        $this->middleware('admin.auth')->only('store','update','destroy','putOn');
    }
}
