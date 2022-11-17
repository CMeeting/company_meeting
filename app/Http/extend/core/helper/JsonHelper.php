<?php

namespace App\Http\extend\core\helper;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 15:33
 */

/**
 * Json 使用
 * @author oShine
 * @since 2017-03-24
 */
class JsonHelper
{
    /**
     * @param $value
     * @param bool $useArray
     * @return mixed
     */
    public static function decode($value, $useArray = true)
    {
        return json_decode($value, $useArray);
    }

    /**
     * @param $value
     * @return string
     */
    public static function encode($value)
    {
        return json_encode($value, true);
    }

    /**
     * @param $result
     * @return string
     */
    public static function JSONReturn($result)
    {
        return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $value
     */
    public static function display($value)
    {
        @header('Content-type: text/json;charset=utf-8');
        echo self::encode($value);
    }

    /**
     * @param $data
     * @return array
     * todo 这里可以优化，引用传值，减少内存重新分配
     */
    public static function format($data)
    {
        //如果data为空，原样返回
        /*if (empty($data) && $data !== 0) {
            return $data;
        }*/
        if ($data === null) {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::format($data[$k]);
            }
        } else {
            settype($data, 'string');
        }

        return $data;
    }

    /**
     * @param $value
     * @return string
     */
    public static function encodeEx($value)
    {
        $value = self::format($value);
        return self::encode($value);
    }

    /**
     * @param $value
     */
    public static function displayEx($value)
    {
        @header('Content-type: text/json;charset=utf-8');
        echo self::encodeEx($value);
    }

}
