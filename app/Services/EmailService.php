<?php

namespace App\Services;

use App\Jobs\SendEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\NewsletterlogModel;

class EmailService
{

    function send_email($data,$arr,$subject,$type=1){
         //根据邮件设置不同的邮件发送账号
         $no_reply = [34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 49, 54, 58, 59];
         $service = [46, 47, 48, 50, 51, 52, 55, 56, 57, 60];
         $news = [53];

         if(in_array($data['id'], $no_reply)){
             MailHelperService::setAccount('no_reply');
         }elseif(in_array($data['id'], $service)){
             MailHelperService::setAccount('service');
         }elseif(in_array($data['id'], $news)){
             MailHelperService::setAccount('news');
         }else{
             MailHelperService::setAccount('no_reply');
         }

         $maile = new NewsletterlogModel();
         foreach ($arr as $k=>$v){
              Mail::send('email',//模板文件
                 ['info' => $data['info']],//模板页面的内容
                 function ($obj) use($v, $subject) {
                     //用邮件对象执行发送的功能
                     try{
                         $obj->to($v)->subject($subject);
                     }catch (\Exception $e){
                         \Log::info("$v:邮件发送异常", $e->getMessage());
                         throw new \Exception("邮件发送失败");
                     }
                 }
             );
              if($type==2){
                  $maile->_update(['status'=>1,'updated_at'=>date("Y-m-d H:i:s")],"association_id='{$data['id']}' and mail='$v' and status!=1");
              }
         }
     }

    public function sendDiyContactEmail($data,$type=1,$email='',$arrs=array())
    {
        $subject = isset($data['title'])?$data['title']:$arrs['title'];//邮件标题
        $arr=explode(',',$email);
        if($type==1){
            $data['info'] = str_replace("#@username","长沙凯钿测试客户名称",$data['info']);
            $data['info'] = str_replace("#@phone","0731-8422****",$data['info']);
            $data['info'] = str_replace("#@code","xxxx-xxxx-xxxx",$data['info']);
            $data['info'] = str_replace("#@paytime","测试时间".date("Y-m-d H:i:s"),$data['info']);
            $data['info'] = str_replace("#@mail","1322061784@qq.com",$data['info']);
            $data['info'] = str_replace("#@product","ComPDF产品名称测试",$data['info']);
        }elseif ($type==3){
            $arrs['info'] = str_replace("(客户的需求编号)",$data['order_no'],$arrs['info']);
            $arrs['info'] = str_replace("(插入网站中记录客户提交的bug的状态链接)","<a href='http://test-compdf.kdan.cn:3026/support-ticket'>http://test-compdf.kdan.cn:3026/support-ticket</a>",$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif ($type==4){
            $arrs['info'] = str_replace("#@username",$data['username'],$arrs['info']);
            $arrs['info'] = str_replace("#@products",$data['products'],$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif ($type==5){
            $arrs['info'] = str_replace("产品下单页面链接",$data['url'],$arrs['info']);
            $arrs['info'] = str_replace("销售邮箱",$data['email'],$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif ($type==6){
            $arrs['info'] = str_replace("(人名)",$data['username'],$arrs['info']);
            $arrs['info'] = str_replace("(订单号) has failed.",$data['orderno'],$arrs['info']);
            $arrs['info'] = str_replace("具体ID号",$data['order_id'],$arrs['info']);
            $arrs['info'] = str_replace("具体产品名",$data['products'],$arrs['info']);
            $arrs['info'] = str_replace("购买时长",$data['pay_years']."/year",$arrs['info']);
            $arrs['info'] = str_replace("总金额",$data['price'],$arrs['info']);
            $arrs['info'] = str_replace("已支付的金额",$data['payprice'],$arrs['info']);
            $arrs['info'] = str_replace("应支付的金额",$data['yesprice'],$arrs['info']);
            $arrs['info'] = str_replace("对应产品的购买页面",$data['url'],$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif($type==7){
            $arrs['info'] = str_replace("具体ID号",$data['order_id'],$arrs['info']);
            $arrs['info'] = str_replace("具体时间",$data['pay_time'],$arrs['info']);
            $arrs['info'] = str_replace("具体产品名",$data['products'],$arrs['info']);
            $arrs['info'] = str_replace("购买时长",$data['pay_years'],$arrs['info']);
            $arrs['info'] = str_replace("产品费用",$data['goodsprice'],$arrs['info']);
            $arrs['info'] = str_replace("税收金额",$data['taxes'],$arrs['info']);
            $arrs['info'] = str_replace("总金额",$data['price'],$arrs['info']);
            $arrs['info'] = str_replace("已支付的金额",$data['payprice'],$arrs['info']);
            $arrs['info'] = str_replace("未付金额",$data['noorderprice'],$arrs['info']);
            $arrs['info'] = str_replace("(产品名)",$data['products'],$arrs['info']);
            $url="<a href='".$data['fapiao']."'>Download invoices</a>";
            $arrs['info'] = str_replace("发票下载链接",$url,$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif ($type==8){
            $arrs['info'] = str_replace("(具体的订单号)","test123",$arrs['info']);
            $arrs['info'] = str_replace("(具体日期)","2022/11/22",$arrs['info']);
            $arrs['info'] = str_replace("(对方的账号信息)","xiaochaomen",$arrs['info']);
            $arrs['info'] = str_replace("登录ComPDFKit用户账户的链接",$data['url'],$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif ($type==9){
            $arrs['info'] = str_replace("具体ID号",$data['order_id'],$arrs['info']);
            $arrs['info'] = str_replace("具体时间",$data['pay_time'],$arrs['info']);
            $arrs['info'] = str_replace("(人名)",$data['username'],$arrs['info']);
            $arrs['info'] = str_replace("具体产品名",$data['products'],$arrs['info']);
            $arrs['info'] = str_replace("购买时长",$data['pay_years'],$arrs['info']);
            $arrs['info'] = str_replace("产品费用",$data['goodsprice'],$arrs['info']);
            $arrs['info'] = str_replace("总金额",$data['price'],$arrs['info']);
            $arrs['info'] = str_replace("税收金额","$0.00",$arrs['info']);
            $arrs['info'] = str_replace("已支付的金额",$data['payprice'],$arrs['info']);
            $arrs['info'] = str_replace("未付金额",$data['noorderprice'],$arrs['info']);
            $arrs['info'] = str_replace("对应产品的支付页面",$data['url'],$arrs['info']);
            $arrs['info'] = str_replace("应支付的金额",$data['price'],$arrs['info']);
            $arrs['info'] = str_replace("发票下载链接",$data['url'],$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif ($type==10){
            $src='http://test-pdf-pro.kdan.cn:3026/unsubscribe?email='.$arr[0];
            $arrs['info'] = str_replace("#@url",$src,$arrs['info']);
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }elseif ($type==11){
            $data['info'] = $arrs['info'];
            $data['id'] = $arrs['id'];
        }
        SendEmail::dispatch($data, $arr, $subject, $type)->delay(Carbon::now()->addMinute());
//        self::send_email($data, $arr, $subject, $type);
    }




}
