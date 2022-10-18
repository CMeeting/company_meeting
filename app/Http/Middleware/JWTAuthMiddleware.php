<?php

namespace App\Http\Middleware;

use App\Services\JWTService;
use App\Services\UserService;
use Closure;

class JWTAuthMiddleware
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
        $token = \Request::header('Authorization');

        //校验token
        $result = JWTService::verifyToken($token);
        if($result['code'] != 200){
            return \Response::json(['code'=>401, 'message'=>$result['msg']]);
        }

        $payload = $result['payload'];
        request()->offsetSet('login_user_email', $payload->email);

        //先更新token 如果是退出登录，注销账号，修改密码等操作后面会删除token
        JWTService::saveToken($payload->email, $payload->jti);

        return $next($request);
    }
}
