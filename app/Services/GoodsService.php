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

use App\Export\GoodsExport;
use App\Export\UserExport;
use App\Models\Goods;
use App\Models\Goodsclassification;
use Auth;

class GoodsService
{
    public function __construct()
    {

    }

    public function data_list($param)
    {
        $where = "deleted=0 and is_saas=0";
        if ($param['info']) {
            $where .= " and {$param['query_type']}={$param['info']}";
        }
        if ($param['level1']) {
            $where .= " and level1={$param['level1']}";
        }
        if ($param['level2']) {
            $where .= " and level2={$param['level2']}";
        }
        if ($param['level3']) {
            $where .= " and level3={$param['level3']}";
        }
        if ($param['status']) {
            $param['status'] = $param['status'] - 1;
            $where .= " and status={$param['status']}";
        }
        if (isset($param['start_date']) && $param['start_date'] && isset($param['end_date']) && $param['end_date']) {
            $where .= " AND created_at BETWEEN '" . $param['start_date'] . "' AND '" . $param['end_date'] . "'";
        } elseif (isset($param['start_date']) && $param['start_date'] && empty($param['end_date'])) {
            $where .= " AND created_at >= '" . $param['start_date'] . "'";
        } elseif (isset($param['end_date']) && $param['end_date'] && empty($param['start_date'])) {
            $where .= " AND created_at <= '" . $param['end_date'] . "'";
        }

        if (isset($param['updated_at']) && $param['updated_at'] && isset($param['endupdated_at']) && $param['endupdated_at']) {
            $where .= " AND updated_at BETWEEN '" . $param['updated_at'] . "' AND '" . $param['endupdated_at'] . "'";
        } elseif (isset($param['updated_at']) && $param['updated_at'] && empty($param['endupdated_at'])) {
            $where .= " AND updated_at >= '" . $param['updated_at'] . "'";
        } elseif (isset($param['endupdated_at']) && $param['endupdated_at'] && empty($param['updated_at'])) {
            $where .= " AND updated_at <= '" . $param['endupdated_at'] . "'";
        }

        if (isset($param['shelf_at']) && $param['shelf_at'] && isset($param['endshelf_at']) && $param['endshelf_at']) {
            $where .= " AND shelf_at BETWEEN '" . $param['shelf_at'] . "' AND '" . $param['endshelf_at'] . "'";
        } elseif (isset($param['shelf_at']) && $param['shelf_at'] && empty($param['endshelf_at'])) {
            $where .= " AND shelf_at >= '" . $param['shelf_at'] . "'";
        } elseif (isset($param['endshelf_at']) && $param['endshelf_at'] && empty($param['shelf_at'])) {
            $where .= " AND shelf_at <= '" . $param['endshelf_at'] . "'";
        }


        $goods = new Goods();


        if ($param['export'] == 1) {
            return $goods->whereRaw($where)->orderByRaw('id desc')->get()->toArray();
        } else {
            $data = $goods->whereRaw($where)->orderByRaw('id desc')->paginate(10);
        }

        if (!empty($data)) {
            $classification = $this->assembly_classification();
            foreach ($data as $k => $v) {
                $v->products = $classification[$v['level1']]['title'];
                $v->platform = $classification[$v['level2']]['title'];
                $v->licensie = $classification[$v['level3']]['title'];
            }
        }
        return $data;
    }


    public function data_listsaas($param)
    {
        $where = "deleted=0 and is_saas=1";
        if ($param['info']) {
            $where .= " and {$param['query_type']}={$param['info']}";
        }
        if ($param['level1']) {
            $where .= " and level1={$param['level1']}";
        }
        if ($param['level2']) {
            $where .= " and level2={$param['level2']}";
        }
        if ($param['status']) {
            $param['status'] = $param['status'] - 1;
            $where .= " and status={$param['status']}";
        }
        if (isset($param['start_date']) && $param['start_date'] && isset($param['end_date']) && $param['end_date']) {
            $where .= " AND created_at BETWEEN '" . $param['start_date'] . "' AND '" . $param['end_date'] . "'";
        } elseif (isset($param['start_date']) && $param['start_date'] && empty($param['end_date'])) {
            $where .= " AND created_at >= '" . $param['start_date'] . "'";
        } elseif (isset($param['end_date']) && $param['end_date'] && empty($param['start_date'])) {
            $where .= " AND created_at <= '" . $param['end_date'] . "'";
        }

        if (isset($param['updated_at']) && $param['updated_at'] && isset($param['endupdated_at']) && $param['endupdated_at']) {
            $where .= " AND updated_at BETWEEN '" . $param['updated_at'] . "' AND '" . $param['endupdated_at'] . "'";
        } elseif (isset($param['updated_at']) && $param['updated_at'] && empty($param['endupdated_at'])) {
            $where .= " AND updated_at >= '" . $param['updated_at'] . "'";
        } elseif (isset($param['endupdated_at']) && $param['endupdated_at'] && empty($param['updated_at'])) {
            $where .= " AND updated_at <= '" . $param['endupdated_at'] . "'";
        }

        if (isset($param['shelf_at']) && $param['shelf_at'] && isset($param['endshelf_at']) && $param['endshelf_at']) {
            $where .= " AND shelf_at BETWEEN '" . $param['shelf_at'] . "' AND '" . $param['endshelf_at'] . "'";
        } elseif (isset($param['shelf_at']) && $param['shelf_at'] && empty($param['endshelf_at'])) {
            $where .= " AND shelf_at >= '" . $param['shelf_at'] . "'";
        } elseif (isset($param['endshelf_at']) && $param['endshelf_at'] && empty($param['shelf_at'])) {
            $where .= " AND shelf_at <= '" . $param['endshelf_at'] . "'";
        }


        $goods = new Goods();

        if ($param['export'] == 1) {
            return $goods->whereRaw($where)->orderByRaw('id desc')->get()->toArray();
        } else {
            $data = $goods->whereRaw($where)->orderByRaw('id desc')->paginate(10);
        }

        if (!empty($data)) {
            $classification = $this->assembly_saasclassification();
            foreach ($data as $k => $v) {
                $v->products = $classification[$v['level1']]['title'];
                $v->platform = $classification[$v['level2']]['title'];
            }
        }
        return $data;
    }

    function assembly_classification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("deleted=0 and is_saas=0",'displayorder');
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }
    function assembly_saasclassification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("deleted=0 and is_saas=1",'displayorder');
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }

    public function threelevellinkage()
    {
        $where = "deleted=0 and is_saas=0";
        $goods = new Goodsclassification();
        $data = $goods->_where($where, "lv,displayorder");

        $lv1 = array(['id'=>0,'title'=>'请选择Products']);
        $lv2 = array();
        $lv3 = array();
        if ($data) {
            foreach ($data as $k => $v) {
                if ($v['lv'] == 1) {
                    $lv1[] = $v;
                }
            }
            foreach ($lv1 as $ks => $vs) {  //循环一级数组数据
                $lv2[$vs['title']][] = ['id'=>0,'title'=>'请选择Platform'];
                $lv3[$vs['id']][][] = ['id'=>0,'title'=>'请选择License Type'];
                foreach ($data as $kb => $vb) {         //循环二级数组数据
                    $s = 0;
                    $a = 0;
                    if ($vb['lv'] == 2 && $vb['pid'] === $vs['id']) {
                        $lv2[$vs['title']][] = $vb;

                            $lv3[$vs['id']][$vb['id']][] = ['id'=>0,'title'=>'请选择License Type'];

                        foreach ($data as $kc => $vc) {  //循环组装三级级数组数据
                            if ($vc['lv'] == 3 && $vc['pid'] === $vb['id']) {
                                $lv3[$vs['id']][$vb['id']][] = $vc;
                                $s++;
                            }
                        }

                    }
                }

                $lv3[$vs['id']] = array_merge($lv3[$vs['id']]);
            }
            $lv2 = array_values($lv2);
            $lv3 = array_merge($lv3);
        }
        return ['arr1' => $lv1, 'arr2' => $lv2, 'arr3' => $lv3];
    }


    public function threelevellinkagesaas()
    {
        $where = "deleted=0 and is_saas=1";
        $goods = new Goodsclassification();
        $data = $goods->_where($where, "lv,displayorder");
        $lv1 = array(['id'=>0,'title'=>'请选择套餐']);
        $lv2 = array();
        if ($data) {
            foreach ($data as $k => $v) {
                if ($v['lv'] == 1) {
                    $lv1[] = $v;
                }
            }
            foreach ($lv1 as $ks => $vs) {  //循环一级数组数据
                $lv2[$vs['title']][] = ['id'=>0,'title'=>'请选择档位'];
                foreach ($data as $kb => $vb) {         //循环二级数组数据
                    $s = 0;
                    if ($vb['lv'] == 2 && $vb['pid'] === $vs['id']) {
                        $lv2[$vs['title']][] = $vb;

                    }
                }
            }
            $lv2 = array_values($lv2);
        }
        return ['arr1' => $lv1, 'arr2' => $lv2];
    }



    public function addEditcaregorical($param)
    {
        $Goods = new Goods();
        if (isset($param['data'])) {
            $data = $param['data'];
        }
        if(isset($data['info'])){
            $data['info']=serialize($data['info']);
        }
        if (isset($data['id'])) {
            $where = "id='{$data['id']}' and is_saas=0";
            $is_find = $Goods->_find($where);
            $is_find = $Goods->objToArr($is_find);
            if (($is_find['level1'] != $data['level1']) || ($is_find['level2'] != $data['level2']) || ($is_find['level3'] != $data['level3'])) {
                $names = $Goods->find("level1={$data['level1']} and level2={$data['level2']} and level3={$data['level3']} and deleted=0 and id!={$data['id']} and is_saas=0");
                $names = $Goods->objToArr($names);
                if ((isset($names) && $names)) {
                    return "repeat";
                }
            }
            $bool = $Goods->_update($data, $where);
        } elseif (isset($param['delid'])) {
            $bool = $Goods->_update(['deleted' => 1], "id=" . $param['delid']);
        } else {
            $names = $Goods->_find("level1='" . $data['level1'] . "' and level2={$data['level2']} and level3={$data['level3']} and deleted=0 and is_saas=0");
            $names = $Goods->objToArr($names);
            if (isset($names) && $names) {
                return "repeat";
            }
            if ($data['status'] == 1) {
                $data['shelf_at'] = date("Y-m-d H:i:s");
            }
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $bool = $Goods->_insert($data);
        }
        return $bool;

    }

    public function addsaasEditcaregorical($param)
    {
        $Goods = new Goods();
        if (isset($param['data'])) {
            $data = $param['data'];
        }
        if (isset($data['id'])) {
            $where = "id='{$data['id']}' and is_saas=1";
            $is_find = $Goods->_find($where);
            $is_find = $Goods->objToArr($is_find);
            if (($is_find['level1'] != $data['level1']) || ($is_find['level2'] != $data['level2']) ) {
                $names = $Goods->find("level1={$data['level1']} and level2={$data['level2']}  and deleted=0 and id!={$data['id']} and is_saas=1");
                $names = $Goods->objToArr($names);
                if ((isset($names) && $names)) {
                    return "repeat";
                }
            }
            $bool = $Goods->_update($data, $where);
        } elseif (isset($param['delid'])) {
            $bool = $Goods->_update(['deleted' => 1], "id=" . $param['delid']);
        } else {
            $names = $Goods->_find("level1='" . $data['level1'] . "' and level2={$data['level2']} and deleted=0 and is_saas=1");
            $names = $Goods->objToArr($names);
            if (isset($names) && $names) {
                return "repeat";
            }
            if ($data['status'] == 1) {
                $data['shelf_at'] = date("Y-m-d H:i:s");
            }
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $data['is_saas'] = 1;
            $bool = $Goods->_insert($data);
        }
        return $bool;

    }

    public function getFindcategorical($id)
    {
        $Goods = new Goods();
        $data = $Goods->_find("deleted=0 and id='{$id}'");
        $data = $Goods->objToArr($data);
        $classification = $this->assembly_classification();
        if($data['info']){
            $data['info'] = unserialize($data['info']);
        }
        $data['level1name'] = $classification[$data['level1']]['title'];
        $data['level2name'] = $classification[$data['level2']]['title'];
        $data['level3name'] = $classification[$data['level3']]['title'];

        return $data;
    }

    public function getsaasFindcategorical($id)
    {
        $Goods = new Goods();
        $data = $Goods->_find("deleted=0 and id='{$id}'");
        $data = $Goods->objToArr($data);
        $classification = $this->assembly_saasclassification();
        if($data['info']){
            $data['info'] = unserialize($data['info']);
        }
        $data['level1name'] = $classification[$data['level1']]['title'];
        $data['level2name'] = $classification[$data['level2']]['title'];
        return $data;
    }

    public function show($param)
    {
        $Goods = new Goods();
        $status = $Goods->_find("id=" . $param['id']);
        $status = $Goods->objToArr($status);
        $enabled = ($status['status'] == 1) ? 0 : 1;
        if ($enabled == 1) {
            $bool = $Goods->_update(['status' => $enabled, 'shelf_at' => date("Y-m-d H:i:s")], "id=" . $param['id']);
        } else {
            $bool = $Goods->_update(['status' => $enabled], "id=" . $param['id']);
        }

        return array('code' => $bool, "status" => $enabled);
    }

    public function export($list, $field)
    {
        $title_arr = [
            'id' => 'ID',
            'level1' => 'Products',
            'level2' => 'Platform',
            'level3' => 'Licensie Type',
            'price' => 'Pricing(USD)',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'shelf_at' => '上架时间',
        ];

        $classification = $this->assembly_classification();

        $field = explode(',', $field);

        $header = [];
        foreach ($field as $title) {
            $header[] = array_get($title_arr, $title);
        }
        $rows[] = $header;

        foreach ($list as $data) {
            $row = [];
            foreach ($field as $key) {
                $value = array_get($data, $key);
                if (in_array($key, ['level1', 'level2', 'level3'])) {
                    $value = $classification[$value]['title'];
                } elseif ($key == 'status') {
                    $value = $value == 1 ? '下架' : '上架';
                }
                $row[] = $value;
            }

            $rows[] = $row;
        }

        $userExport = new GoodsExport($rows);
        $fileName = 'export' . DIRECTORY_SEPARATOR . '商品列表' . time() . '.xlsx';
        \Excel::store($userExport, $fileName);

        //ajax请求 需要返回下载地址，在使用location.href请求下载地址
        return ['url' => route('download', ['file_name' => $fileName])];
    }


    public function get_data()
    {
        $Goodsclassification = new Goodsclassification();
        $goods = new Goods();
        $data = $Goodsclassification->_where("deleted=0 and is_saas=0", 'lv,displayorder');
        $goodsdata = $goods->whereRaw("deleted=0 and status=1 and is_saas=0")->orderByRaw('id desc')->get()->toArray();
        if (!empty($goodsdata)) {
            $classification = $this->assembly_classification();
            foreach ($goodsdata as $k => $v) {
                if($v['info']){
                    $goodsdata[$k]['info'] = unserialize($v['info']);
                }else{
                    $goodsdata[$k]['info'] = [];
                }
                $goodsdata[$k]['products'] = $classification[$v['level1']]['title'];
                $goodsdata[$k]['platform'] = $classification[$v['level2']]['title'];
                $goodsdata[$k]['licensie'] = $classification[$v['level3']]['title'];
            }
        }
        $arr = array();
        //第一层循环组装产品数据
        foreach ($data as $k => $v) {
            if ($v['lv'] == 1) {
                $arr[] = ['id' => $v['id'], 'name' => $v['title']];
            }
        }
        //第二层循环组装平台数据数据
        foreach ($arr as $k => $v) {
            foreach ($data as $ks => $vs) {
                if ($v['id'] == $vs['pid']) {
                    $licensetype = array();
                    //第三层循环嵌套分类数据
                    foreach ($data as $kk => $vv) {
                        $goodsarr = array();
                        if ($vs['id'] == $vv['pid']) {
                            //最后一层循环嵌套组装商品数据
                            if (!empty($goodsdata)) {
                                foreach ($goodsdata as $kev => $val) {
                                    if ($val['level3'] == $vv['id']) {
                                        $goodsarr = $val;
                                    }
                                }
                            }
                            if(isset($goodsarr['id'])){
                                $licensetype[] = ['id' => $vv['id'], 'name' => $vv['title'], 'goods' => $goodsarr];
                            }
                        }
                    }
                    if(count($licensetype)>0){
                        $arr[$k]['Platform'][] = ['id' => $vs['id'], 'name' => $vs['title'], 'licensetype' => $licensetype];
                    }
                }
            }
//            if(!isset($arr[$k]['Platform'])){
//                unset($arr[$k]);
//            }
        }
        return $arr;

    }

}