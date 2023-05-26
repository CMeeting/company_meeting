<?php


namespace App\Services;


use Carbon\Carbon;

class CommonService
{
    public static function formatDate($date){
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $date_obj = Carbon::parse($date);
        $month  = $date_obj->month;
        $month_index = $month-1;
        $month_en = $months[$month_index];
        $format = $date_obj->format('d, Y, H:i');

        return $month_en . ' ' . $format;
    }

    /**
     * 根据邮箱@前面内容的长度 截取当前时间戳从最后一位开始的该长度内容 并MD5
     * @param $str
     * @return false|string
     */
    public static function getSignByStr($str){
        $time = time();
        $str_len = strlen($str);
        $time_len = strlen($time);
        if($str_len > $time_len){
            $str_len = $time_len;
        }

        return substr($time, -1, $str_len);
    }

    public static function getTokenByEmail($email){
        $sign = self::getSignByStr($email);
        return JWTService::base64UrlEncode($sign . $email);
    }

    public static function getEmailByToken($token){
        $token = JWTService::base64UrlDecode($token);
        $sign = self::getSignByStr($token);

        $len = strlen($sign);
        return substr($token, $len);
    }

    public static function createUuid($prefix = ''){
        $chars = md5(uniqid(mt_rand()));
        $uuid = substr ( $chars, 0, 8 ) . '-'
            . substr ( $chars, 8, 4 ) . '-'
            . substr ( $chars, 12, 4 ) . '-'
            . substr ( $chars, 16, 4 ) . '-'
            . substr ( $chars, 20, 12 );

        return $prefix.$uuid ;
    }
}