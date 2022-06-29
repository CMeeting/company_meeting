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
 * Time: 上午10:35
 */

namespace App\Repositories;


use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RolesRepository
{
    /**
     * 获取所有角色
     * @return mixed
     */
    public function getRoles()
    {
        return Role::get();
    }

    public function getrolesinfo(){
         $data=Db::table("rules")
            ->selectRaw("parent_id as pid,id,name")
            ->get();
         $data=$this->objToArr($data);
         $arr=array();
         foreach ($data as $k=>$v){
             if($v['pid']==0){
                 $arr[]=[
                     'id'=>$v['id'],
                     'name'=>$v['name'],
                     'pid'=>$v['pid'],
                     'sub'=>$this->assembly_data($v['id'],$data)
                 ];
             }
         }
         return $arr;
    }

    function assembly_data($id,$data,$arr=array()){
        foreach ($data as $k=>$v){
          if($v['pid']==$id){
              $arr[]=[
                  'id'=>$v['id'],
                  'name'=>$v['name'],
                  'pid'=>$v['pid'],
                  'sub'=>$this->assembly_data($v['id'],$data)
              ];
          }
        }
      return $arr;
    }

    public function objToArr($object) {

        //先编码成json字符串，再解码成数组

        return json_decode(json_encode($object), true);

    }
}