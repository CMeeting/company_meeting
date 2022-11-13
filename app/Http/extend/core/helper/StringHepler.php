<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2018/8/14
 * Time: 16:22
 */

namespace core\helper;


class StringHepler
{
    public static function str_compare($str1, $str2)
    {
        $arr1 = explode('.', $str1);
        $arr2 = explode('.', $str2);
        for ($i = 0; $i < count($arr1); $i++) {
            if ($arr1[$i] > $arr2[$i]) {
                return $str1;
            } elseif ($arr1[$i] < $arr2[$i]) {
                return $str2;
            }
        }
        return $str1;
    }
    public static function validEmail($uid){
        $regex= '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        return preg_match($regex,$uid);
    }
    /**
     * 下划线转驼峰
     * 思路:
     * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     */
    public static function camelize($uncamelized_words,$separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        $newstr = substr($uncamelized_words,0,strlen($uncamelized_words)-1);
        return ucwords(ltrim(str_replace(" ", "", ucwords($newstr)), $separator ));
    }

    /**
     * 驼峰命名转下划线命名
     * 思路:
     * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     */
    public static function uncamelize($camelCaps,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
    //判断https
    public static function httpProtocol()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return 'https://';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return 'https://';
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return 'https://';
        }
        return 'http://';
    }


    /**
     * 判断https
     * @return bool
     */
    public static function isHTTPS()
    {
        if (defined('HTTPS') && HTTPS) return true;
        if (!isset($_SERVER)) return FALSE;
        if (!isset($_SERVER['HTTPS'])) return FALSE;
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return TRUE;
        } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
            return TRUE;
        } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function buildInsertData(array $data): array
    {
        $key = '';
        $val = '';
        foreach ($data as $k => $v) {
            if ($v === 0) {
                $val .= $v . ',';
            }else if ($v === null) {
                $v = 'null';
                $val .= $v . ',';
            } else {
                $val .= '\'' . $v . '\',';
            }
            $key .= '"'.$k.'"' . ',';
        }
        $result['key'] = rtrim($key, ',');
        $result['val'] = rtrim($val, ',');
        return $result;
    }

    public static function substrUuid($length = 19)
    {
        return strtoupper(substr(self::uuid(), 4, $length));
    }
    /**
     * 获取uuid
     * @return string
     */
    public static function uuid(): string
    {
        if (function_exists('com_create_guid')) {
            $delete_last = substr(com_create_guid(), 0, -1);
            $delete_fist = substr($delete_last, 1);
            return strtolower($delete_fist);
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtolower(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
            return strtolower($uuid);
        }
    }

    /**
     * @return false|string
     */
    public static function time($time = '')
    {
        $time = empty($time) ? time() : $time;
        return date('Y-m-d H:i:s', $time);
    }

    /**
     * 创建随机字符串
     * @param int $length
     * @return string
     */
    public static function createNonceStr($length = 8,$bool = false)
    {
        if($bool){
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }else{
            $chars = 'abcdefghijklmnopqrstuvwxyz';
        }
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function createNumberCode($length = 6)
    {
        $chars = '1234567890';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * @param $url
     * @param string $user
     * @param string $pw
     * @return float
     */
    public static function getsize($url, $user = '', $pw = '')
    {

        // start output buffering
        ob_start();
        // initialize curl with given uri
        $ch = curl_init($url); // make sure we get the header
        curl_setopt($ch, CURLOPT_HEADER, 1); // make it a http HEAD request
        curl_setopt($ch, CURLOPT_NOBODY, 1); // if auth is needed, do it here
        if (!empty($user) && !empty($pw)) {
            $headers = array('Authorization: Basic ' . base64_encode($user . ':' . $pw));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $okay = curl_exec($ch);
        curl_close($ch); // get the output buffer
        $head = ob_get_contents(); // clean the output buffer and return to previous // buffer settings
        ob_end_clean();  // gets you the numeric value from the Content-Length // field in the http header
        $regex = '/Content-Length:\s([0-9].+?)\s/';
        $count = preg_match($regex, $head, $matches);  // if there was a Content-Length field, its value // will now be in $matches[1]
        if (isset($matches[1])) {
            $size = $matches[1];
        } else {
            $size = 'unknown';
        }
        $last_mb = round($size / (1024 * 1024), 3);
        $last_kb = round($size / 1024, 3);
        return ceil($last_kb);
    }

    /**
     * @param $string
     * @return bool|string
     */
    public static function trimBom($string)
    {
        if (!$string) {
            return $string;
        }
        $charset[1] = substr($string, 0, 1);
        $charset[2] = substr($string, 1, 1);
        $charset[3] = substr($string, 2, 1);

        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
            $string = substr($string, 3);
        }

        return $string;
    }

    /**
     * @return string
     */
    public static function createOrderSn($str = '')
    {
        return $str . date('Ymdhi', time()) . self::get_rand_num(4);
    }

    /**
     * 获取指定长度的随机数字
     * @param int $len 字符串长度
     * @return string 返回指定长度字符串
     */
    public static function get_rand_num($len = 6)
    {
        $rand_arr = range('0', '9');
        shuffle($rand_arr);//打乱顺序
        $rand = array_slice($rand_arr, 0, $len);
        return implode('', $rand);
    }

    /**
     * 根据数字变成缩写单位
     * @param int $len 字符串长度
     * @return string 返回指定长度字符串
     */
    public static function getNumberConvert($num, $is_int)
    {
        if ($num > 0) {
            if ($num > 0 && $num <= 999) {
                $retrun_num = $num;
            } elseif ($num >= 1000 && $num <= 9999) {
                $retrun_num = '999+';
            } elseif ($num >= 10000 && $num <= 99999999) {
                if ($is_int == 1) {
                    //整形取正
                    $retrun_num = intval($num / 10000) . '万';
                } else {
                    $retrun_num = ($num / 10000) . '万';
                }
            } elseif ($num >= 100000000) {
                if ($is_int == 1) {
                    //整形取正
                    $retrun_num = intval($num / 100000000) . '亿';
                } else {
                    $retrun_num = ($num / 100000000) . '亿';
                }
            }
            return $retrun_num;
        } else {
            return $num;
        }
    }

    /**
     * 按符号截取字符串的指定部分
     * @param string $str 需要截取的字符串
     * @param string $sign 需要截取的符号
     * @param int $number 如是正数以0为起点从左向右截 负数则从右向左截
     * @return string 返回截取的内容
     */
    public static function cut_str($str, $sign, $number)
    {
        $array = explode($sign, $str);
        $length = count($array);
        if ($number < 0) {
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if ($abs_number > $length) {
                return 'error';
            } else {
                return $new_array[$abs_number - 1];
            }
        } else {
            if ($number >= $length) {
                return 'error';
            } else {
                return $array[$number];
            }
        }
    }
}