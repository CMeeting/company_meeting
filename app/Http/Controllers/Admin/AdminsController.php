<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminRequest;
use App\Services\OssService;
use Illuminate\Http\Request;
use App\Services\AdminsService;
use App\Services\ActionLogsService;
use App\Repositories\RolesRepository;
use App\Http\Requests\Admin\AdminLoginRequest;

class AdminsController extends BaseController {
    protected $adminsService;
    protected $rolesRepository;
    protected $actionLogsService;

    /**
     * AdminsController constructor.
     * @param AdminsService $adminsService
     * @param RolesRepository $rolesRepository
     */
    public function __construct(AdminsService $adminsService, RolesRepository $rolesRepository,ActionLogsService $actionLogsService)
    {
        $this->adminsService = $adminsService;
        $this->actionLogsService = $actionLogsService;
        $this->rolesRepository = $rolesRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $admins = $this->adminsService->getAdminsWithRoles();

        return $this->view(null, compact('admins'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $groupids=$this->actionLogsService->getadmingroupids();
        $ruleids=$this->actionLogsService->getadminrousids();
        $roles = $this->rolesRepository->getRoles();
        $rolesinfo = $this->rolesRepository->getrolesinfo();
        $rolesarr = json_encode($this->rolesRepository->getrolesarr());
        return view('admin.admins.create', compact('roles','rolesinfo','rolesarr','groupids','ruleids'));
    }

    /**
     * @param BlogTagRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminRequest $request)
    {
        if (isset($request->all()['name']) && $request->all()['name']){
            $row = $this->adminsService->ByName($request->all()['name']);
            if($row){
                flash('用户名已存在，请检查！')->error()->important();
            }else{
                $this->adminsService->create($request);

                flash('添加管理员成功')->success()->important();
            }
        }else{
            flash('请填写用户名')->error()->important();
        }

        return redirect()->route('admins.index');
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request,$id)
    {
        //$dadmin_id = session('id');
        $admin = $this->adminsService->ById($id);
        $groupids=$this->actionLogsService->getadmingroupids();
        $ruleidsc=$this->actionLogsService->getadminrousids();
        $roles = $this->rolesRepository->getRoles();
        $rolesinfo = $this->rolesRepository->getrolesinfo();
        $rolesarr = json_encode($this->rolesRepository->getrolesarr());
        $adminroles = $this->rolesRepository->admingetrolesarr($id);
        return view('admin.admins.edit', compact('admin', 'roles','rolesinfo','rolesarr','adminroles','groupids','ruleidsc'));
    }

    /**
     * @param BlogTagRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (isset($request->all()['name']) && $request->all()['name']){
            $row = $this->adminsService->exceptIdAndName($id,$request->all()['name']);
            if($row){
                flash('用户名已存在，请检查！')->error()->important();
            }else{
                $this->adminsService->update($request, $id);
                flash('更新资料成功')->success()->important();
            }
        }else{
            flash('请填写用户名')->error()->important();
        }

        return redirect()->route('admins.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $admin = $this->adminsService->ById($id);

        if (empty($admin)) {
            flash('删除失败')->error()->important();

            return redirect()->route('admins.index');
        }


        $admin->roles()->detach();

        $admin->delete();


        flash('删除成功')->success()->important();

        return redirect()->route('admins.index');
    }

    public function del(){
        $param = request()->input();
        if(!empty($param['id'])){
            $row = $this->adminsService->del($param['id']);
            if($row){
                $data['code'] = 0;
                flash('删除成功')->success()->important();
            }else{
                $data['code'] = 1;
                $data['msg'] = '删除失败';
                flash('删除失败')->error()->important();
            }
        }else{
            $data['code'] = 1;
            $data['msg'] = '参数有误，请重试';
            flash('删除失败')->error()->important();
        }
        return $data;
    }

    public function editAvatar($id){
        $admin = $this->adminsService->ById($id);
        return view('admin.admins.edit_avatar',compact('admin'));
    }

    public function updateAvatar(Request $request,$id){
        $admin = $this->adminsService->ById($id);
        if (empty($admin)) {
            return viewError('操作失败', 'index.index');
        }
        if($request->avatr){
            $result = OssService::uploadFile($request->avatr);
            if (200 == $result['code']) {
                $avatr = str_replace('http://', 'https://', $result['data']['url']);
                $admin->update(['avatr' => $avatr]);
                return viewError('头像修改成功!', 'index.index', 'success');
            }else{
                return viewError('图片上传OSS失败', 'index.index');
            }
        }else{
            return viewError('修改失败，请检查图片是否正确上传', 'index.index');
        }
    }

    public function editPassword($id){
        $admin = ['id'=>$id];
        return view('admin.admins.edit_password',compact('admin'));
    }

    public function updatePassword(Request $request,$id){
        $admin = $this->adminsService->ById($id);
        if (empty($admin)) {
            return viewError('操作失败', 'index.index');
        }
        $data = $request->all();
        $back = $this->adminsService->updatePassword($data,$admin);
        if($back){
            if (500 == $back['code']) {
                return ['code'=>1000,'msg'=>$back['msg']];
            }else{
                return ['code'=>200,'msg'=>$back['msg']];
            }
        }else{
            return viewError('操作失败', 'index.index');
        }
    }

    /**
     * @param $status
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status($status, $id)
    {
        $admin = $this->adminsService->ById($id);

        if (empty($admin)) {
            flash('操作失败')->error()->important();

            return redirect()->route('admins.index');
        }

        $admin->update(['status' => $status]);

        flash('更新状态成功')->success()->important();

        return redirect()->route('admins.index');
    }

    public function showLoginForm()
    {
        return view('admin.admins.login');
    }

    /**
     * 管理员登陆
     * @param AdminLoginRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function loginHandle(AdminLoginRequest $request)
    {
        echo"<pre>";
        print_r($request);die;
        $result = $this->adminsService->login($request);

        if ( !$result || false == $result) {
            return viewError('登录失败', 'login');
        }

        return viewError('登录成功!', 'index.index', 'success');
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $this->adminsService->logout();

        return redirect()->route('login');
    }
}
