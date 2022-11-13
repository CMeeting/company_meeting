<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2018/6/15
 * Time: 17:02
 */
namespace core\common;


use Firebase\JWT\JWT;

class ResponseHead extends Head
{
    public $code = 0;       //业务code,例如错误code
    public $message = '';    //错误信息

    private function __construct($token, $time, $code, $message)
    {
        $this->time = $time;
        $this->code = $code;
        $this->message = $message;
        $this->token = $token;
    }

    public static function build($token, $code = 0, $message = '')
    {
        $time = time();
        return new self($token, $time, $code, $message);
    }

    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }

    /**
     * 构建jwt
     * @param array $tokenParam
     * @param $key
     * @return string
     */
    public static function buildToken(array $tokenParam, $key)
    {
        $jwt = JWT::encode($tokenParam, $key);
        return $jwt;
    }
}