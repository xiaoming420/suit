<?php

namespace App\Http\Middleware;

use App\Models\users;
use Closure;
use Illuminate\Support\Facades\Redis;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token_request = JWTAuth::setRequest($request);
        $token = $token_request->getToken();
        if (empty($token)) {
            fun_respon(0, '缺少授权', 404);
        }
        try {
            $token = $token_request->getToken()->__toString();
            if (!$token) {
                fun_respon(0, '授权不存在');
            }
            $claims = $token_request->parseToken()->getPayload()->toArray();
        } catch (\Exception $e) {
            fun_respon(0, '授权无效', 404);
        }

        $redis_token = Redis::get('token_' . $claims['sub']);
        //$redis_token = isset($_SESSION['token_' . $claims['sub']]) ? $_SESSION['token_' . $claims['sub']] : '';
        if (!$redis_token) {
            fun_respon(0, '授权已过期', 404);
        }
        $userinfo = users::userinfo($claims['sub']);
        if (!$userinfo) {
            fun_respon(0, '授权已过期!', 404);
        }
        $request->offsetSet('unionid', $claims['sub']);
        $request->offsetSet('validate_uid', $userinfo['id']);
        return $next($request);
    }
}
