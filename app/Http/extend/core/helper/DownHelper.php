<?php
/**
 * Created by PhpStorm.
 * User: 10619
 * Date: 2019/7/18
 * Time: 11:51
 */

namespace core\helper;


class DownHelper
{
    public static function download($file_url, $new_name = '')
    {
        if (!isset($file_url) || trim($file_url) == '') {
            echo '500';
        }
        if (!file_exists($file_url)) { //检查文件是否存在
            echo '404';
        }
        $file_name = basename($file_url);
        $file_type = explode('.', $file_url);
        $file_type = $file_type[count($file_type) - 1];
        $file_name = trim($new_name == '') ? $file_name : urlencode($new_name);
        $file_type = fopen($file_url, 'r'); //打开文件
        //输入文件标签
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: " . filesize($file_url));
        header("Content-Disposition: attachment; filename=" . $file_name);
        ob_clean();
        flush();
        //输出文件内容
        echo fread($file_type, filesize($file_url));
        fclose($file_type);
        die;
    }
}