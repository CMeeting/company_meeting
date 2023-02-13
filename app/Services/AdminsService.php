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
 * Date: 2017/11/13
 * Time: 上午9:50
 */

namespace App\Services;

use Auth;
use App\Handlers\ImageUploadHandler;
use App\Repositories\AdminsRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Repositories\RolesRepository;
use Illuminate\Support\Facades\DB;
use Cache;
class AdminsService
{
    protected $uploader;
    protected $menu_cache = '_menu_cache'; //菜单缓存key
    protected $adminsRepository;

    protected $actionLogsService;

    /**
     * AdminsService constructor.
     * @param AdminsRepository $adminsRepository
     * @param ImageUploadHandler $imageUploadHandler
     * @param ActionLogsService $actionLogsService
     */
    public function __construct(AdminsRepository $adminsRepository, ImageUploadHandler $imageUploadHandler,ActionLogsService $actionLogsService,RolesRepository $rolesRepository)
    {
        $this->uploader = $imageUploadHandler;
        $this->adminsRepository = $adminsRepository;
        $this->actionLogsService = $actionLogsService;
        $this->rolesRepository = $rolesRepository;
    }

    /**
     * 创建管理员数据
     * @param $request
     * @return mixed
     */
    public function create($request)
    {
        $datas = $request->all();
        //上传头像
        $file = Input::file("avatr");
        if ($file) {
//            $result = $this->uploader->save($file, 'avatrs');
//            if ($result) {
//                $datas['avatr'] = $result['path'];
//            }
            $result = OssService::uploadFile($file);
            if (200 == $result['code']) {
                $datas['avatr'] = str_replace('http://', 'https://', $result['data']['url']);
            }
        }

        $datas['password'] = Hash::make($request->password);
        $datas['create_ip'] = $request->ip();
        $datas['last_login_ip'] = $request->ip();
        $rolesarr = $this->rolesRepository->getrolesarr();
        $admin = $this->adminsRepository->create($datas);
        $id= DB::getPdo()->lastInsertId();

        //插入模型关联数据
        $admin->roles()->attach($request->role_id);
        $i=0;
        $arr=array();
        if(count($datas['role_id'])>0){
            //第一层循环，循环所勾选了什么角色
            foreach ($datas['role_id'] as $k=>$v){
                $i=$v;
                //判断角色ID下标的数组是否存在
                if(isset($rolesarr[$v])){
                    //第二层循环，循环角色ID下标数组内权限ID是否被勾选
                    if(isset($datas['rules_id']) && count($datas['rules_id'])>0) {
                        foreach ($rolesarr[$v] as $ks => $vs) {
                            if (in_array($vs, $datas['rules_id'])) {
                                $arr[] = [
                                    'admin_id' => $id,
                                    'role_id' => $v,
                                    'rule_id' => $vs,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s")
                                ];
                            }
                        }
                    }
                }
            }
        }
        $arr[]=[
            'admin_id'=>$id,
            'role_id'=>$i,
            'rule_id'=>2,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s")
        ];
        $arr[]=[
            'admin_id'=>$id,
            'role_id'=>$i,
            'rule_id'=>1,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s")
        ];
        DB::table('admin_auth')->insert($arr);
        return $admin;
    }

    public function del($id){
        $row = Db::table('admins')
            ->where('id','=',$id)
            ->delete();
        return $row??'';
    }

    /**
     * 更新管理员资料
     * @param $request
     * @param $id
     * @return mixed
     */
    public function update($request, $id)
    {
        $datas = $request->all();

        $admin = $this->adminsRepository->ById($id);

        //上传头像
        if ($request->avatr) {
//            $result = $this->uploader->save($request->avatr, 'avatrs');
//            if ($result) {
//                $datas['avatr'] = $result['path'];
//            }
            $result = OssService::uploadFile($request->avatr);
            if (200 == $result['code']) {
                $datas['avatr'] = str_replace('http://', 'https://', $result['data']['url']);
            }
        }

        if (isset($datas['password'])) {
            $datas['password'] = Hash::make($request->password);
        } else {
            unset($datas['password']);
        }
        $admin->update($datas);

        //更新关联表数据
        $admin->roles()->sync($request->role_id);

        $rolesarr = $this->rolesRepository->getrolesarr();
        $arr=array();
        $i=0;
        if(isset($datas['role_id']) && count($datas['role_id'])>0){
            //第一层循环，循环所勾选了什么角色
            foreach ($datas['role_id'] as $k=>$v){
                $i=$v;
                //判断角色ID下标的数组是否存在
                if(isset($rolesarr[$v])){
                    //第二层循环，循环角色ID下标数组内权限ID是否被勾选
                    if(isset($datas['rules_id']) && count($datas['rules_id'])>0){
                        foreach ($rolesarr[$v] as $ks=>$vs){
                            if(in_array($vs,$datas['rules_id'])){
                                $arr[]=[
                                    'admin_id'=>$id,
                                    'role_id'=>$v,
                                    'rule_id'=>$vs,
                                    'created_at'=>date("Y-m-d H:i:s"),
                                    'updated_at'=>date("Y-m-d H:i:s")
                                ];
                            }
                        }
                    }

                }
            }
        }
        DB::table('admin_auth')->where("admin_id","=",$id)->delete();
        $arr[]=[
            'admin_id'=>$id,
            'role_id'=>$i,
            'rule_id'=>2,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s")
        ];
        $arr[]=[
            'admin_id'=>$id,
            'role_id'=>$i,
            'rule_id'=>1,
            'created_at'=>date("Y-m-d H:i:s"),
            'updated_at'=>date("Y-m-d H:i:s")
        ];
        DB::table('admin_auth')->insert($arr);
        return $admin;
    }

    public function updatePassword($data,$admin){
        $result = [];
        switch ($data){
            case empty($data):
                $result['code'] = 500;
                $result['msg'] = '参数错误，请重试';
                break;
            case empty($data['old_password']):
                $result['code'] = 500;
                $result['msg'] = '请填写：原始密码';
                break;
            case empty($data['new_password']):
                $result['code'] = 500;
                $result['msg'] = '请填写：新密码';
                break;
            case empty($data['check_password']):
                $result['code'] = 500;
                $result['msg'] = '请填写：确认新密码';
                break;
            case 1 != Hash::check($data['old_password'],$admin->password):
                $result['code'] = 500;
                $result['msg'] = '原始密码错误，请检查';
                break;
            case $data['check_password'] != $data['new_password']:
                $result['code'] = 500;
                $result['msg'] = '新密码与确认新密码不一致，请检查';
                break;
            default:
                $admin->update(['password'=>Hash::make($data['new_password'])]);
                $result['code'] = 200;
                $result['msg'] = '修改密码成功，请重新登录';
                break;
        }
        return $result;
    }

    /**
     * 获取管理员的详细资料
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return $this->adminsRepository->ById($id);
    }
    /**
     * 根据name获取管理员的详细资料
     * @param $name
     * @return mixed
     */
    public function ByName($name)
    {
        return $this->adminsRepository->ByName($name);
    }

    public function exceptIdAndName($id,$name)
    {
        return $this->adminsRepository->exceptIdAndName($id,$name);
    }

    /**
     * 获取管理员列表 with ('roles')
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminsWithRoles()
    {
        return $this->adminsRepository->getAdminsWithRoles();
    }


    /**
     * 登录管理员
     * @param $request
     * @return bool
     */
    public function login($request)
    {

        if(!Auth::guard('admin')->attempt([
            'name'     => $request->name,
            'password' => $request->password,
            'status'   => 1,
        ])){
            //记录登录操作记录
            $this->actionLogsService->loginActionLogCreate($request,false);
            return false;
        }

        //增加登录次数.
        $admin = Auth::guard('admin')->user();
        session(['id' => $admin->id]);
        $admin->increment('login_count');
        $name=$request->name;
         Db::table("admins")
            ->whereRaw("name='{$name}'")
            ->update(['logintime'=>date("Y-m-d H:i:s")]);

        //记录登录操作记录
        $this->actionLogsService->loginActionLogCreate($request,true);

        return true;
    }

    /**
     * 退出登录
     * @return mixed
     */
    public function logout()
    {
        Cache::tags('rbac')->flush();
        return Auth::guard('admin')->logout();
    }
}