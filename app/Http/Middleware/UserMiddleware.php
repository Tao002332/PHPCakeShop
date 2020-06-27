<?php

namespace App\Http\Middleware;

use App\Enums\ResponseCode;
use App\Http\Controllers\Api\Traits\ApiResponseTraits;
use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class UserMiddleware extends  BaseMiddleware
{


    use ApiResponseTraits;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request);
        try {
            $token_role=$this->auth->parseToken()->getClaim('role');
            if ($token_role != 'user') {
                return $this->fail(ResponseCode::NOT_USER_AUTH);
            }
            return $next($request);
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', 'User role error');
        }
    }
}
