<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\Tree;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Repositories\RulesRepository;
use Illuminate\Http\Request;

class RolesController extends BaseController
{
    /**
     * 展示所有角色
     * @param Role $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Role $role)
    {
        $roles = $role->orderByRaw('`order` ASC , id DESC')->paginate(10);

        return $this->view(null,compact('roles'));
    }

    /**
     * 展示角色页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return $this->view();
    }

    /**
     * 添加角色
     * @param RoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleRequest $request, Role $role)
    {
        if (isset($request->all()['name']) && $request->all()['name']){
            $row = $role->_find("name = '".$request->all()['name']."'");
            if($row){
                flash('角色名称已存在，请检查！')->error()->important();
            }else{
                $role->fill($request->all());
                $role->save();

                flash('添加角色成功')->success()->important();
            }
        }else{
            flash('请填写角色名称')->error()->important();
        }

        return redirect()->route('roles.index');
    }


    /**
     * @param Role $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Role $role)
    {
        return $this->view('edit',compact('role'));
    }

    /**
     * @param RoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RoleRequest $request, Role $role)
    {
        $arr = explode('/',$request->all()['s']);
        $id = $arr[count($arr)-1];
        if (isset($request->all()['name']) && $request->all()['name']){
            $row = $role->_find("id != ".$id." AND name = '".$request->all()['name']."'");
            if($row){
                flash('角色名称已存在，请检查！')->error()->important();
            }else{
                $role->update($request->all());

                flash('修改成功')->success()->important();
            }
        }else{
            flash('请填写角色名称')->error()->important();
        }

        return redirect()->route('roles.index');
    }

    /**
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        $role->rules()->detach(); //删除关联数据

        $role->delete();

        flash('删除成功')->success()->important();

        return redirect()->route('roles.index');
    }

    public function del(RulesRepository $rulesRepository){
        $param = request()->input();
        if(!empty($param['id'])){
            $row = $rulesRepository->del($param['id']);
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
    /**
     * 展示分配权限页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function access(Role $role,RulesRepository $rulesRepository,Tree $tree)
    {
        $rules = $rulesRepository->getRules();

        $datas = $tree::channelLevel($rules, 0, '&nbsp;', 'id','parent_id');

        $rules = $role->rules->pluck('id')->toArray();

        return $this->view(null,compact('role','datas','rules'));
    }

    /**
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function groupAccess(Request $request,Role $role)
    {
        $ids=$request->get('rule_id');
        $ids[]="1";
        $ids[]="2";
        $role->rules()->sync($ids);

        flash('授权成功')->success()->important();

        return redirect()->route('roles.index');
    }
}
