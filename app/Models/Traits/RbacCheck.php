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
 * Time: 下午2:30
 */

namespace App\Models\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Handlers\Tree;
use App\Repositories\RulesRepository;
use Cache;

trait RbacCheck
{
    // 缓存相关配置
    protected $cache_key = '_cache_rules';

    protected $menu_cache = '_menu_cache'; //菜单缓存key

    /**
     * 获取当前用户的所有权限
     * @return mixed
     */
    public function getRules()
    {
        $cache_key = $this->id . $this->cache_key;

        if(!Cache::tags(['rbac', 'rules'])->has($cache_key))
       {
            $permissions = [];
            $data=Db::table("admin_auth")->whereRaw("admin_id='{$this->id}'")->selectRaw("rule_id")->get();
            $data=$this->objToArr($data);
            $arr=[];
            foreach ($data as $k=>$v){
                $arr[]=$v['rule_id'];
            }
            $arr=array_unique($arr);
            $rote=Db::table("rules")->selectRaw("route,id")->get();
            $rote=$this->objToArr($rote);
            foreach ($arr as $k=>$v){
                foreach ($rote as $ks=>$vs){
                    if($v==$vs['id']){
                        $permissions[]=$vs['route'];
                    }
                }
            }
            /**获得当前用户所有权限路由*/
            $permissions = array_unique($permissions);
            /**将权限路由存入缓存中*/
             Cache::tags(['rbac', 'rules'])->forever($cache_key, $permissions);
        }

        return Cache::tags(['rbac', 'rules'])->get($cache_key);
    }

    /**
     * 获取树形菜单导航栏
     * @return array
     */
    public function getMenus()
    {
        $menu_cache = $this->id . $this->menu_cache;

        if (!Cache::tags(['rbac', 'menus'])->has($menu_cache))
        {
            $rules = [];
            //判断是否是超级管理员用户组
            if (in_array(1, $this->roles->pluck('id')->toArray()))
            {
                //超级管理员用户组获取全部权限数据
                $rules = (new RulesRepository())->getRulesAndPublic()->toArray();

            } else {
                $data=Db::table("admin_auth")->whereRaw("admin_id='{$this->id}'")->selectRaw("rule_id")->get();
                $data=$this->objToArr($data);
                $arr=[];
                foreach ($data as $k=>$v){
                    $arr[]=$v['rule_id'];
                }
                $arr=array_unique($arr);
                foreach ($this->roles as $role)
                {
                    $rules = array_merge($rules, $role->rulesPublic()->toArray());
                }
                if($rules)
                {
                    foreach ($rules as $k=>$v){
                        if(!in_array($v['id'],$arr)){
                            unset($rules[$k]);
                        }
                    }
                    $rules = unique_arr($rules);
                }
            }

            /**将权限路由存入缓存中*/
            Cache::tags(['rbac', 'menus'])->put($menu_cache, $rules,86400);
        }


        $rules = Cache::tags(['rbac', 'menus'])->get($menu_cache);

        return Tree::array_tree($rules);
    }

    /**
     * 删除权限缓存和菜单缓存
     * @return bool
     */
    public function clearRuleAndMenu()
    {
        return Cache::tags('rbac')->flush();
    }

    public function objToArr($object) {

        //先编码成json字符串，再解码成数组

        return json_decode(json_encode($object), true);

    }
}