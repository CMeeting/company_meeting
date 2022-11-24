<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Models\NewsletterlogModel;

class EmailService
{

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
            $data['info'] = $arrs['info'];
        }
        $res=$this->send_email($data,$arr,$subject,$type);
    }

     function send_email($data,$arr,$subject,$type=1){
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




}
