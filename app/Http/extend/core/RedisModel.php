<?php
/**
 * Created by PhpStorm.
 * User: LZZ
 * Date: 2019/5/7
 * Time: 14:34
 */

namespace core;


use think\cache\driver\Redis;
use think\facade\Config;

class RedisModel extends Redis
{

    /**
     * @var
     */
    private static $instance;

    /**
     * @param array $options
     * @return RedisModel
     */
    private static function getInstance(array $options = [])
    {
        if (!self::$instance) {
            if (empty($options)) {
                $options = Config::get('database.redis');
            }
            self::$instance = new self($options);
        }
        return self::$instance;
    }

    /**
     * @param array $options
     * @return RedisModel
     */
    public static function model(array $options = [])
    {
        return self::getInstance($options);
    }

    /**
     * @param array $options
     * @return object
     */
    public static function redis(array $options = [])
    {
        return self::model($options)->handler();
    }


}