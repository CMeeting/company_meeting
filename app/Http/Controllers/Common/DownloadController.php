<?php


namespace App\Http\Controllers\Common;


class DownloadController
{
    /**
     * 下载文件
     * @param $file_name
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($file_name)
    {
        return \Storage::download($file_name);
    }
}