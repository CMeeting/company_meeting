<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/8/7
 * Time: 13:21
 */

namespace core;


class Register
{
    public static $objects;

    public static function set($key, $value)
    {
        self::$objects[$key] = $value;
    }

    public static function get($key)
    {
        return self::$objects[$key];
    }

    public static function unset($key)
    {
        unset(self::$objects[$key]);
    }
}