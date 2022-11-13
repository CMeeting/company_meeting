<?php
/**
 * Created by PhpStorm.
 * User: 10619
 * Date: 2019/7/16
 * Time: 14:54
 */

namespace core;

use Workerman\Worker;
require_once dirname(dirname(__DIR__)).'/vendor/workerman/workerman/Autoloader.php';

// mail worker，和调用端使用Text协议通讯
$mail_worker = new Worker('Text://0.0.0.0:8887');
// 如果发送邮件很慢，mail进程数可以根据需要多开一些
$mail_worker->count = 5;
$mail_worker->name = 'EmailWorker';
$mail_worker->onMessage = function($connection, $url)
{
    $ch = curl_init();

// 设置URL和相应的选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
// 抓取URL并把它传递给浏览器
    $curl_result = curl_exec($ch);

// 关闭cURL资源，并且释放系统资源
    curl_close($ch);

    // 直接返回ok，避免调用端长时间等待
    $connection->send('ok');
    // 假设发来的是json数据
};

    Worker::runAll();
