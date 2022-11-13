<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2018/6/15
 * Time: 16:47
 */
namespace core\common;

class RequestHead extends Head
{
    public $version;
    public $platform;
    public $recode;
    public $excode;

    private function __construct($token, $time, $version, $platform, $excode)
    {
        $this->token = $token;
        $this->time = $time;
        $this->version = $version;
        $this->platform = $platform;
        $this->excode = $excode;
    }

    public static function build($head)
    {
        $excode = isset($head['excode']) ? isset($head['excode']) : '';
        return new self($head['token'], $head['time'], $head['version'], $head['platform'],  $excode);
    }

    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }

    public function verifyToken($key)
    {
        $jwtInfo = \Firebase\JWT\JWT::decode($this->token, $key, array('HS256'));
        return $jwtInfo;
    }
}