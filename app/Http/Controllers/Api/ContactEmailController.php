<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\ContactEmailService;
use App\Services\EmailService;
use App\Services\OssService;
use Illuminate\Http\Request;

class ContactEmailController extends Controller
{

    const ALLOW_EXT = ['gif','png','jpg','jpeg','doc','docx','xls','xlsx','csv','pdf','rar','zip','txt','mp4','flv'];

    public function support(Request $request){
        $validate = \Validator::make($request->all(),[
           'email' => 'required|email',
           'first_name' => 'required',
           'last_name' => 'required',
           'subject' => 'required',
           'description' => 'required'
        ]);

        //文件校验
        $files = $request->allFiles();
        if(count($files) > 3){
            return \Response::json(['code'=>500, 'message'=>'最多只能上传三个文件']);
        }

        foreach ($files as $file){
            $size = $file->getSize();
            if($size > 1024 * 1024 *30){
                return \Response::json(['code'=>500, 'message'=>'上传文件大小不能超过30M']);
            }
            $suffix = $file->getClientOriginalExtension();
            if(!in_array($suffix, self::ALLOW_EXT)){
                return \Response::json(['code'=>500, 'message'=>'上传文件类型错误']);
            }
        }

        if($validate->fails()){
            return \Response::json(['code'=>500, 'message'=>$validate->messages()->first()]);
        }

        $email = $request->input('email');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $subject = $request->input('subject');
        $description = $request->input('description');

        //新增support
        $contact_email_service = new ContactEmailService();
        $id = $contact_email_service->add($email, $first_name, $last_name, $subject, $description, $files);

        //上传附件
        $paths = [];
        $files = $request->allFiles();
        foreach ($files as $file){
            $path = OssService::uploadFileNew($file, 'support');
            $path = str_replace('http', 'https', $path);
            $paths[] = $path;

            //新增邮件附件
            $contact_email_service->addAttachments($id, $path);
        }

        //发送邮件
        $email = 'pengjianyong@kdanmobile.com';
        $email_service = new EmailService();
        $email_service->sendEmail($description, $subject, $email, $paths);

        return \Response::json(['code'=>200, 'message'=>'success']);
    }
}