<?php


namespace App\Http\Controllers\Api;

use App\Services\EmailService;
use App\Services\MailmagicboardService;
use App\Services\OrdersService;
use App\Services\UserService;
use Illuminate\Http\Request;

class EmailtestContr
{
     public function emailtest(Request $request){
         $email = new EmailService();
         $maile = new MailmagicboardService();
         $param = $request->all();
         $mailedatas = $maile->getFindcategorical($param['id']);
         $emailarr=[];
         $emailarr['email']="support@compdf.com";
         if($param['type']==8){
             $emailarr['url']="http://test-pdf-pro.kdan.cn:3026/login";
             $mailedatas['title'] = str_replace("订单号","test123",$mailedatas['title']);
         }else{
             $emailarr['url']="http://test-pdf-pro.kdan.cn:3026/order/checkout";
         }
         $email->sendDiyContactEmail($emailarr,$param['type'],"1322061784@qq.com,pengjianyong@kdanmobile.com,shuwei@kdanmobile.com,wangyuting@kdanmobile.com",$mailedatas);

     }
}
