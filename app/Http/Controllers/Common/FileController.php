<?php


namespace App\Http\Controllers\Common;


use App\Services\OrdersService;
use Auth;
use Illuminate\Http\Request;
use PDF;

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

    /**
     * html转发票
     * @param Request $request
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function getInvoice(Request $request){
        $html = $request->input('html');

        $times = time() . rand(100, 999);
        if (!file_exists(public_path().DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR)) mkdir(public_path().DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR, 0777);
        $save = public_path().DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR.$times.'.pdf';
        $goodsService = new OrdersService;
        $host = $goodsService->headerurl();
        $url=$host . '/pdf/' . $times.'.pdf';
        PDF::loadHtml($html)->save($save);

        return \Response::json(['url'=>$url]);
    }
}