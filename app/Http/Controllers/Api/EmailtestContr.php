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
         $emailarr['url']="http://test-pdf-pro.kdan.cn:3026/order/checkout";
         $emailarr['email']="support@compdf.com";
         if($param['type']==8){

         }
         $email->sendDiyContactEmail($emailarr,$param['type'],"1322061784@qq.com,pengjianyong@kdanmobile.com,shuwei@kdanmobile.com,wangyuting@kdanmobile.com",$mailedatas);

     }
}
