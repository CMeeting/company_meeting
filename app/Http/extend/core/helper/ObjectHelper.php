<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2018/8/8
 * Time: 10:23
 */

namespace App\Http\extend\core\helper;


class ObjectHelper
{
    /**
     * 对象转数组
     * @param $obj
     * @return array|mixed
     */
    public static function obj2Array($obj)
    {
        if (is_array($obj) || !is_object($obj)) {
            return [];
        }

        return json_decode(json_encode($obj), true);
    }


    public static function convertObjectToArray($object = NULL)
    {
        $array = (array)$object;
        foreach ($array as $key => $val) {
            if (is_object($val)) {
                $val = self::convertObjectToArray($val);

            }
            $array[$key] = $val;
        }

        return $array;

    }
}