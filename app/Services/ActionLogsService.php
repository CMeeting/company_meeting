<?php
/**
 * YICMS
 * ============================================================================
 * 版权所有 2014-2017 YICMS，并保留所有权利。
 * 网站地址: http://www.yicms.vip
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Created by PhpStorm.
 * Author: kenuo
 * Date: 2017/11/17
 * Time: 下午4:38
 */

namespace App\Services;

use Auth;
use Route;
use Zhuzhichao\IpLocationZh\Ip;
use App\Repositories\RulesRepository;
use App\Repositories\ActionLogsRepository;
use Illuminate\Support\Facades\DB;

class ActionLogsService
{
    protected $rulesRepository;

    protected $actionLogsRepository;

    /**
     * ActionLogsService constructor.
     * @param $actionLogsRepository
     */
    public function __construct(RulesRepository $rulesRepository, ActionLogsRepository $actionLogsRepository)
    {
        $this->rulesRepository = $rulesRepository;

        $this->actionLogsRepository = $actionLogsRepository;
    }

    /**
     * 登录操作日志
     * @param $request
     * @return mixed
     */
    public function loginActionLogCreate($request,$status = false)
    {
        //获取当前登录管理员信息
        $admin = Auth::guard('admin')->user();

        $ip = $request->getClientIp();

        $address = Ip::find($ip);

        $info = json_encode($request->all());
        $action = $status ? "管理员: {$admin->name} 登录成功！登录请求参数：{$info}" : " 登录失败,登录的账号为：{$request->name}　密码为：{$request->password} 登录请求参数：{$info}";

        $data = [
            'ip'=> $ip,
            'address'=> $address[0].$address[1].$address[2],
            'action'=> $action,
        ];

        $datas['data'] = json_encode($data);
        $datas['type'] = 2;
        $datas['admin_id'] = $admin->id ;
        return $this->actionLogsRepository->create($datas);
    }
    public function getadmingroupids(){
        $admin = Auth::guard('admin')->user();
        $damin_auth=DB::table("admin_role")->selectRaw("role_id")->whereRaw("admin_id=".$admin->id)->get();
        $arr=json_decode(json_encode($damin_auth), true);
        $data=[];
        foreach ($arr as $k=>$v){
            $data[]=$v['role_id'];
        }
        return $data;
    }
    public function getadminrousids(){
        $admin = Auth::guard('admin')->user();
        $damin_auth=DB::table("admin_auth")->selectRaw("rule_id")->whereRaw("admin_id=".$admin->id)->get();
        $arr=json_decode(json_encode($damin_auth), true);
        $data=[];
        foreach ($arr as $k=>$v){
            $data[]=$v['rule_id'];
        }
        //$data=array_unique($data);
        return $data;
    }

    /**
     * 后台操作日志
     * @param $request
     * @return mixed
     */
    public function mudelActionLogCreate($request)
    {
        $path = Route::currentRouteName();

        $rule = $this->rulesRepository->ByRoute($path);

        if(is_null($rule)) return false;

        //获取当前操作方法上级模块名称
        if($rule->parent_id != 0)
        {
            $parent_rule = $this->rulesRepository->ById($rule->parent_id);
        }

        //获取当前登录管理员信息
        $admin = Auth::guard('admin')->user();

        $address = Ip::find($request->getClientIp());

        $action = "管理员: {$admin->name} 操作了 【{$parent_rule->name}】- {$rule->name} 模块。请求参数：".json_encode($request->all());

        $data = [
            'ip'=> $request->getClientIp(),
            'address'=> $address[0].$address[1].$address[2],
            'action'=> $action,
        ];

        $datas['admin_id'] = $admin->id;
        $datas['data'] = json_encode($data);
        $datas['type'] = 1;
        return $this->actionLogsRepository->create($datas);
    }

    /**
     * 获取全部的操作日志
     * @return mixed
     */
    public function getActionLogs($data)
    {
        return $this->actionLogsRepository->getWithAdminActionLogs($data);
    }
}