<?php
/**
 * Created by PhpStorm.
 * User: LZZ
 * Date: 2019/4/16
 * Time: 14:18
 */

namespace App\Http\extend\core\helper;

use think\facade\Config;

class SysHelper
{
    public static function isEnv($env) {
        return SysHelper::getEnv('env') === $env;
    }

    public static function isServer($serverMode) {
        return SysHelper::getEnv('server_mode') === $serverMode;
    }

    /**
     * 获取配置文件
     * @param $name
     * @return array|mixed
     */
    public static function getConf($name)
    {
        if (empty($name)) {
            return [];
        }
        return Config::get($name . '.') ? Config::get($name . '.') : [];
    }

    /**
     * 获取system配置
     * @param $name
     * @return mixed|string
     */
    public static function getSysConf($name)
    {
        if (empty($name)) {
            return '';
        }
        $system = self::getConf('systemConfig');
        return isset($system[$name]) ? $system[$name] : '';
    }

    /**
     * @param $name
     * @return mixed|string
     */
    public static function getEnv($name)
    {
        if (empty($name)) {
            return '';
        }
        $system = self::getConf('env');
        return isset($system[$name]) ? $system[$name] : '';
    }

    /**
     * 获取错误码
     * @param $name
     * @return mixed|string
     */
    public static function getServicecode($name){
        if (empty($name)) {
            return '';
        }
        $system = self::getConf('servicecode');
        return isset($system[$name]) ? $system[$name] : '';
    }

    /**
     * 获取第三方配置
     * @param $name
     * @return mixed|string
     */
    public static function getThirdparty($name){
        if (empty($name)) {
            return '';
        }
        $system = self::getConf('thirdparty');
        return isset($system[$name]) ? $system[$name] : '';
    }
    /**
     * 获取文件上传信息
     * @param $name
     * @return array|mixed
     */
    public static function getConfFilePath($name)
    {
        if (empty($name)) {
            return [];
        }
        $filepath = SysHelper::getThirdparty('oss_path');
        if (!isset($filepath[$name])) {
            return [];
        }

        $filepathInfo = $filepath[$name];
        if (!isset($filepathInfo['path'])) {
            return [];
        }

        if (empty($filepathInfo['size'])) {
            $filepathInfo['size'] = self::getSysConf('default_upload_size');
        }

        if (empty($filepathInfo['allow'])) {
            switch (explode('-', $name)[0]) {
                case '01':
                    $filepathInfo['allow'] = self::getSysConf('default_img_allow');
                    break;
                case '02':
                    $filepathInfo['allow'] = self::getSysConf('default_media_allow');
                    break;
                default:
                    //
            }
        }
        return $filepathInfo;
    }

}