<?php
/**
 * Created by PhpStorm.
 * User: 10619
 * Date: 2019/7/11
 * Time: 16:52
 */

namespace App\Http\extend\core\helper;

use think\facade\Env;

class LogHelper
{
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARN = 'WARN';
    const LEVEL_ERROR = 'ERROR';
    const LEVELS = ['DEBUG' => 0, 'INFO' => 1, 'WARN' => 2, 'ERROR' => 3];

    public static function shouldLog($logLevel) {
        $minLevel = SysHelper::getEnv('log_level');
        if (empty($minLevel)) $minLevel = self::LEVEL_INFO;
        if (isset(self::LEVELS[$logLevel])) {
            return self::LEVELS[$logLevel] >= self::LEVELS[$minLevel];
        } else {
            LogHelper::logSpider('Invalid logLevel: '.$logLevel, self::LEVEL_ERROR);
            return true;
        }
    }

    public static function logEmail($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('mail_record.log', $log_content, $logLevel);
    }

    public static function logDevice($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('devices.log', $log_content, $logLevel);
    }

    public static function logTest($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('testt.log', $log_content, $logLevel);
    }

    public static function logSubs($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('subscription.log', $log_content, $logLevel);
    }

    public static function logParam($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('param.log', $log_content, $logLevel);
    }

    public static function logSpider($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('spider.log', $log_content, $logLevel);
    }

    public static function logCheckServer($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('checkServer.log', $log_content, $logLevel);
    }

    public static function logUpload($log_content, $logLevel = self::LEVEL_INFO)
    {
        self::logByFilename('upload.log', $log_content, $logLevel);
    }

    public static function logByFilename($filename, $log_content, $logLevel = self::LEVEL_INFO)
    {
        if (!self::shouldLog($logLevel)) return false;
        $log_dir = Env::get('ROOT_PATH') . 'runtime/log/';
        !is_dir($log_dir) && mkdir($log_dir, 0755, true);

        if (is_array($log_content)) {
            $log_content = JsonHelper::JSONReturn($log_content);
        }
        file_put_contents($log_dir . $filename, PHP_EOL.'[' . date("Y-m-d H:i:s") . '] ' .$logLevel.' '.$log_content, FILE_APPEND);
    }

}