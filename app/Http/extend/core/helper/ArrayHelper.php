<?php
/**
 * Created by PhpStorm.
 * User: Lzz
 * Date: 2019/4/8
 * Time: 10:23
 */

namespace core\helper;


use think\paginator\driver\Bootstrap;

class ArrayHelper
{
    public static function countDimension($array = []) {
        if (is_array(reset($array))) {
            $dimension = self::countDimension(reset($array)) + 1;
        } else {
            $dimension = 1;
        }
        return $dimension;
    }

    public static function buildDataBykeys(array $arr, array $data, array $keys)
    {
        $res = self::filterByKeys($data, $keys);
        return array_merge($arr, $res);
    }
    /**
     * 过滤数组中key没在$keys中
     * @param array $data 需要过滤的数组
     * @param array $keys 需要保持key中数据列表
     * @return array
     */
    public static function filterByKeys(array $data, array $keys)
    {
        if (empty($data) || empty($keys)) {
            return [];
        }
        $keys = array_flip($keys);
        return array_intersect_key($data, $keys);
    }

    /**
     * 过滤数组中key没在$keys中
     * @param array $data 需要过滤的数组
     * @param array $keys 需要保持key中数据列表
     * @return array
     */
    public static function filterDiffByKeys(array $data, array $keys)
    {
        if (empty($data) || empty($keys)) {
            return [];
        }
        $keys = array_flip($keys);
        return array_diff_key($data, $keys);
    }

    /**
     * 给console首页用的月份
     * @param array $array
     * @param array $baseArray
     * @param $column
     * @param array $data
     * @return array
     */
    public static function fillArray(array $array, array $baseArray, $column, array $data)
    {

        $result = [];
        foreach ($baseArray as $key => $item) {
            foreach ($array as $value) {
                $data[$column] = $item;
                $result[$key] = $data;
                if ($value[$column] == $item) {
                    $result[$key] = $value;
                    break;
                }
            }

        }
        return $result;
    }

    /**
     * 提取二维数组中某个字段成为key
     * @param array $array
     * @param $column
     * @return array
     */
    public static function multiColumn2Key(array $array, $column)
    {
        $newArray = [];
        $keys = array_column($array, $column);
        if (count($keys) <= 0) {
            return [];
        }
        foreach ($array as $item) {
            $newArray[$item[$column]][] = $item;
        }
        return $newArray;
    }


    public static function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }

        return $obj;
    }

    /**
     * 根据指定键值排序去重
     * @param $arr
     * @param $key
     * @return array
     */
    public static function arr_uniq($arr, $key, $field)
    {
        $arr = self::sortArray($arr, $field, 'DESC');
        $arr = array_values($arr);
        $key_arr = [];
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $key_arr)) {
                unset($arr[$k]);
            } else {
                $key_arr[] = $v[$key];
            }
        }
        return $arr;
    }
    /**
     * @param $array
     * @param $keys
     * @param string $type
     * @return array
     */
    public static function sortArray($array, $keys, $type = 'ASC')
    {
//$array为要排序的数组,$keys为要用来排序的键名,$type默认为升序排序
        $keysvalue = $new_array = array();
        foreach ($array as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'ASC') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $array[$k];
        }
        return $new_array;
    }
    //此处对数组进行降序排列

    public static  function assoc_unique($arr, $key)
    {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        $arr = array_values($arr); //sort函数对数组进行排序
        return $arr;
    }

    /**
     * @param array $data
     * @param int $curpage
     * @param int $rows
     * @return \think\Paginator
     */
    public static function array_page(array $data, $curpage = 1, $rows = 25)
    {
        $dataTo = array_chunk($data, $rows);
        if ($dataTo) {
            $showdata = count($dataTo) < $curpage ? 1 : $dataTo[$curpage - 1];
        } else {
            $showdata = null;
        }
        $p = Bootstrap::make($showdata, $rows, $curpage, count($data), false, [
            'var_page' => 'page',
            'path' => substr(url(), 0, -11),//这里根据需要修改url
            'query' => [],
            'fragment' => '', 1
        ]);
        return $p;
    }

    /**
     * @param $data
     * @param $page
     * @return mixed
     */
    public static function render($data,$page)
    {
        return self::array_page($data,$page)->appends($_GET)->render();
    }


    public static function multi_array_unqie($arr){
        $data = array();
        foreach ($arr as $val){
            if(!in_array($val,$data)){
                $data[] = $val;
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @param $page
     * @return array
     */
    public static function toArray($data,$page)
    {
        return self::array_page($data,$page)->appends($_GET)->toArray();
    }

    /**
     * @param $data
     */
    public static function filterTrim(&$data)
    {
        if(is_array($data)){
            foreach ($data as &$val) {
                if(is_array($val)){
                    self::filterTrim($val);
                }else{
                    $val = trim($val);
                }
            }
        }else{
            $data = trim($data);
        }
    }
}