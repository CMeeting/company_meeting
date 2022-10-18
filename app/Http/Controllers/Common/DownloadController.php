<?php


namespace App\Http\Controllers\Common;


use Illuminate\Http\Request;

class DownloadController
{
    /**
     * 下载文件
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Request $request)
    {
        $file_name = $request->input('file_name');
        return \Storage::download($file_name);
    }
}