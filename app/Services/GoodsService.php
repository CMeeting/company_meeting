<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/8/10
 * @Time: 15:54
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

declare (strict_types=1);

namespace App\Services;

use App\Models\Goods;
use App\Models\Goodsclassification;
use Auth;

class GoodsService
{
    public function __construct()
    {

    }

    public function data_list($param){
        $where = "deleted=0";
        if($param['info']){
             $where.=" and {$param['query_type']}={$param['info']}";
        }
        if($param['status']){
            $param['status']=$param['status']-1;
            $where.=" and status={$param['status']}";
        }
        if(isset($param['start_date'])&&$param['start_date'] && isset($param['end_date'])&&$param['end_date']){
            $where.=" AND created_at BETWEEN '".$param['start_date']."' AND '".$param['end_date']."'";
        }elseif (isset($param['start_date'])&&$param['start_date'] && empty($param['end_date'])){
            $where.=" AND created_at >= '".$param['start_date']."'";
        }elseif (isset($param['end_date'])&&$param['end_date'] && empty($param['start_date'])){
            $where.=" AND created_at <= '".$param['end_date']."'";
        }
        $goods = new Goods();
        $data = $goods->whereRaw($where)->orderByRaw('id desc')->paginate(10);
        if(!empty($data)){
            $classification=$this->assembly_classification();
            foreach ($data as $k=>$v){
                $v->products=$classification[$v['level1']]['title'];
                $v->platform=$classification[$v['level2']]['title'];
                $v->licensie=$classification[$v['level3']]['title'];
            }
        }
        return $data;
    }

    function assembly_classification(){
        $Goodsclassification = new Goodsclassification();
        $data=$Goodsclassification->_where("deleted=0");
        $arr=array();
        foreach ($data as $k=>$v){
            $arr[$v['id']]=$v;
        }
        return $arr;
    }

    public function threelevellinkage(){
        $where = "deleted=0";
        $goods = new Goodsclassification();
        $data = $goods->_where($where,"lv");

        $lv1=array();
        $lv2=array();
        $lv3=array();
        if($data){
            foreach ($data as $k=>$v){
                if($v['lv']==1){
                   $lv1[]=$v;
                }
            }
            foreach ($lv1 as $ks=>$vs){  //循环一级数组数据
                foreach ($data as $kb=>$vb){         //循环二级数组数据
                    if($vb['lv']==2 && $vb['pid']==$vs['id']){
                        $lv2[$vs['title']][]=$vb;

                        foreach ($data as $kc=>$vc){  //循环组装三级级数组数据
                            if($vc['lv']==3 && $vc['pid']==$vb['id']){
                                $lv3[$vs['id']][$vb['id']][]=$vc;
                            }
                        }

                    }
                }
                $lv3[$vs['id']]=array_merge($lv3[$vs['id']]);
            }
            $lv2=array_values($lv2);
            $lv3=array_merge($lv3);
        }
        return ['arr1'=>$lv1,'arr2'=>$lv2,'arr3'=>$lv3];
    }



    public function addEditcaregorical($param){
        $Goods = new Goods();
        if(isset($param['data'])){
            $data = $param['data'];
        }
        if (isset($data['id'])) {
            $where = "id='{$data['id']}'";
            $is_find = $Goods->_find($where);
            $is_find = $Goods->objToArr($is_find);
            if (($is_find['level1'] != $data['level1'])||($is_find['level2'] != $data['level2'])||($is_find['level3'] != $data['level3'])) {
                $names = $Goods->find("level1={$data['level1']} and level2={$data['level2']} and level3={$data['level3']} and deleted=0 and id!={$data['id']}");
                $names = $Goods->objToArr($names);
                if ((isset($names) && $names)) {
                    return "repeat";
                }
            }
            $bool = $Goods->_update($data, $where);
        } elseif (isset($param['delid'])) {
            $bool = $Goods->_update(['deleted' => 1], "id=" . $param['delid']);
        } else {
            $names = $Goods->_find("level1='" . $data['level1'] . "' and level2={$data['level2']} and level3={$data['level3']} and deleted=0");
            $names = $Goods->objToArr($names);
            if (isset($names) && $names) {
                return "repeat";
            }
            if($data['status']==1){
                $data['shelf_at'] = date("Y-m-d H:i:s");
            }
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $bool = $Goods->_insert($data);
        }
        return $bool;

    }

    public function getFindcategorical($id){
        $Goods = new Goods();
        $data = $Goods->_find("deleted=0 and id='{$id}'");
        $data = $Goods->objToArr($data);
        $classification=$this->assembly_classification();

        $data['level1name']=$classification[$data['level1']]['title'];
        $data['level2name']=$classification[$data['level2']]['title'];
        $data['level3name']=$classification[$data['level3']]['title'];;

        return $data;
    }

    public function show($param)
    {
        $Goods = new Goods();
        $status = $Goods->_find("id=" . $param['id']);
        $status= $Goods->objToArr($status);
        $enabled = ($status['status'] == 1) ? 0 : 1;
        if($enabled==1){
            $bool = $Goods->_update(['status' => $enabled,'shelf_at'=>date("Y-m-d H:i:s")], "id=" . $param['id']);
        }else{
            $bool = $Goods->_update(['status' => $enabled], "id=" . $param['id']);
        }

        return array('code'=>$bool,"status"=>$enabled);
    }

}