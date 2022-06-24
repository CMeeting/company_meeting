<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin\Count;
use App\Http\Requests\Admin\BlogTagRequest;
use Illuminate\Http\Request;
use App\Services\AdminsService;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Controllers\Admin\BaseController;
use App\Repositories\RulesRepository;
use App\Models\Role;
use App\Repositories\RolesRepository;
use App\Models\Admin;
use App\Models\ConfigModel;
/**
 * Description of AdminController
 *
 * @author 七彩P1
 */
class AdminController extends BaseController {
    //put your code here
    protected $adminsService;

    protected $rolesRepository;

    /**
     * AdminsController constructor.
     * @param AdminsService $adminsService
     * @param RolesRepository $rolesRepository
     */
    public function __construct(AdminsService $adminsService, RolesRepository $rolesRepository)
    {
        $this->adminsService = $adminsService;

        $this->rolesRepository = $rolesRepository;
    }
    //账户管理
    public function management(Request $request)
    {
        $admins = $this->adminsService->getAdminsWithRoles();
        $levelArr = (new ConfigModel)->level;
        $level = $request->get("level")?$request->get("level"):0;
        return view("admin.count.admin.index", compact('admins','levelArr','level'));
    }
    
    //用户渠道显示
    public function pfidIndex($level,Request $request)
    {
        $levelArr = (new ConfigModel)->level;
        $pfidArr = (new ConfigModel)->pfid;
        $usidArr = (new ConfigModel)->usid;
        $level = $request->level;
        $admins = (new Admin)->where("level",$level)->paginate(20);
        return view("admin.count.admin.pfidIndex", compact('admins','levelArr','level','pfidArr','usidArr'));
    }
    //用户渠道创建
    public function createPfid($level,Request $request)
    {
        $showKeyArr = $levelArr = (new ConfigModel)->level;
        $pfidArr = (new ConfigModel)->pfid;
        $usidArr = (new ConfigModel)->usid;
        $level = $request->level;
        $id = $request->get("id")?$request->get("id"):0;
        if($id){
            $item = (new Admin)->where("id",$id)->first();;
        }else{
            $item ="";
        }
        if($level ==1){
            $selectArr = $pfidArr;
        }elseif($level ==2){
            $selectArr = $usidArr;
        }
        return view("admin.count.admin.create", compact('item','levelArr','level','pfidArr','usidArr','showKeyArr'));
    }
    
     //厂商渠道
    public function usidIndex()
    {
        $admins = $this->adminsService->getAdminsWithRoles();

        return view("admin.admins.index", compact('admins'));
    }
    //厂商渠道创建
    public function createUsid()
    {
        $admins = $this->adminsService->getAdminsWithRoles();

        return view("admin.admins.index", compact('admins'));
    }
    
        //厂商
    public function threeIndex()
    {
        $admins = $this->adminsService->getAdminsWithRoles();

        return view("admin.admins.index", compact('admins'));
    }
    //角色
    public function role1(Role $role)
    {
        $roles = $role->paginate(10);

        return view("admin.roles.index",compact('roles'));
    }
}
