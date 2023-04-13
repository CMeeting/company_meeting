<?php


namespace App\Http\Controllers\Common;


use Auth;
use Illuminate\Http\Request;

class FileController
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

    public function upload(Request $request){
        $file = $request->file('file');
        $admin = Auth::guard('admin')->user();
        $filename = 'licenseKey' . DIRECTORY_SEPARATOR . $admin->id;
        \Storage::putFile($filename, $file, 'private_key.pem');
    }
}