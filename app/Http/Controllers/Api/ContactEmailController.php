<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\ContactEmailService;
use App\Services\EmailService;
use App\Services\OssService;
use Illuminate\Http\Request;

class ContactEmailController extends Controller
{

    const ALLOW_EXT = ['gif','png','jpg','jpeg','doc','docx','xls','xlsx','csv','pdf','rar','zip','txt'];

    /**
     * support
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function support(Request $request){
        $validate = \Validator::make($request->all(),[
           'email' => 'required|email',
           'first_name' => 'required',
           'last_name' => 'required',
           'subject' => 'required',
           'description' => 'required'
        ]);

        if($validate->fails()){
            return \Response::json(['code'=>500, 'message'=>$validate->messages()->first()]);
        }

        $email = $request->input('email');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $subject = $request->input('subject');
        $description = $request->input('description');
        $files = $request->input('files');

        //新增support
        $contact_email_service = new ContactEmailService();
        $id = $contact_email_service->add($email, $first_name, $last_name, $subject, $description);

        //保存附件信息
        if(!empty($files)){
            foreach ($files as $file){
                $contact_email_service->addAttachments($id, $file);
            }
        }

        //发送邮件
        $email = 'pengjianyong@kdanmobile.com';
        $email_service = new EmailService();

        $description .= "<br/><br/><div>Email：$email</div><div>First Name：$first_name</div><div>Last Name：$last_name</div>";
        $email_service->sendEmail($description, $subject, $email, $files);

        return \Response::json(['code'=>200, 'message'=>'success']);
    }

    /**
     * 上传附件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAttachments(Request $request){
        $file = $request->file('file');

        if(!$file){
            return \Response::json(['code'=>501, 'message'=>'invalid file']);
        }

        $size = $file->getSize();
        if($size > 1024 * 1024 * 30){
            return \Response::json(['code'=>502, 'message'=>'invalid size']);
        }
        $suffix = $file->getClientOriginalExtension();
        if(!in_array($suffix, self::ALLOW_EXT)){
            return \Response::json(['code'=>503, 'message'=>'invalid file type']);
        }

        $url = OssService::uploadFileNew($file, 'support');
        $url = str_replace('http', 'https', $url);

        return \Response::json(['code'=>200, 'message'=>'success', 'url'=>$url]);
    }
}