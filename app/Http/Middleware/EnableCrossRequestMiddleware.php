<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class EnableCrossRequestMiddleware
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
        $response = $next($request);
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '*';
        $headers = [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Headers' => 'Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Authorization , Access-Control-Request-Headers, X-CSRF-TOKEN',
            'Access-Control-Expose-Headers' => 'Authorization, authenticated',
            'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE, OPTION',
            'Access-Control-Allow-Credentials' => 'true'
        ];

        switch ($response) {
            // 普通的http请求
            case ($response instanceof Response) :
                foreach ($headers as $key => $value) {
                    $response->header($key, $value);
                }
                break;
            // laravel-excel
            case ($response instanceof \Symfony\Component\HttpFoundation\Response):
                foreach ($headers as $key => $value) {
                    $response->headers->set($key, $value);
                }
                break;
        }

        return $response;
    }
}
