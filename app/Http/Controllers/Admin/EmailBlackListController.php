<?php


namespace App\Http\Controllers\Admin;


use App\Services\EmailBlacklistService;
use Illuminate\Http\Request;

class EmailBlackListController extends BaseController
{
    /**
     * 取消/加入邮箱黑名单
     * @param Request $request
     * @return array
     */
    public function store(Request $request){
        $email = $request->input('email');
        $type = $request->input('type');

        $service = new EmailBlacklistService();
        if($type == 'add'){
            $exits = $service->exitsEmail($email);
            if(!$exits){
                $service->add($email);
            }
        }else{
            $service->del($email);
        }

        return ['code'=>200, 'message'=>'success'];
    }

}