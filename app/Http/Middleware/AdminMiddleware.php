<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\Traits\ApiResponseTraits;
use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;


class AdminMiddleware extends  BaseMiddleware
{

    use ApiResponseTraits;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request);
        try {
            $token_role=$this->auth->parseToken()->getClaim('role');
            if ($token_role != 'admin') {
                throw new UnauthorizedHttpException('jwt-auth', 'User role error');
            }
            return $next($request);
        } catch (JWTException $e) {
            return $this->fail(ResponseCode::NOT_ADMIN_AUTH);
        }
    }
}
