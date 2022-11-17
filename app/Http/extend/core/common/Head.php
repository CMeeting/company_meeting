<?php
/**
 * Created by PhpStorm.
 * User: LZZ
 * Date: 2019/4/16
 * Time: 13:55
 */

namespace core\common;

use Firebase\JWT\JWT;

abstract class Head
{

    public $token;
    public $time;

    public abstract function toArray();

    /**
     * 验证jwt
     * @param $key
     * @return object
     */
    public function verifyToken($key)
    {
        $jwtInfo = JWT::decode($this->token, $key, ['HS256']);
        return $jwtInfo;
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