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
use Carbon\Carbon;

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

    /**
     * 商品列表-SaaS
     * @param $param
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function dataListSaaS($param)
    {
        $query = Goods::query()
            ->leftJoin('goods_classification', 'goods.level1', '=', 'goods_classification.id')
            ->where('goods.deleted', 0)
            ->where('goods.is_saas', 1);

        if (isset($param['info']) && $param['info']) {
            $query->where('goods.' . $param['query_type'], $param['info']);
        }
        if (isset($param['level1']) && $param['level1']) {
            $query->where('goods.level1', $param['level1']);
        }
        if (isset($param['level2']) && $param['level2']) {
            $query->where('goods.level2', $param['level2']);
        }
        if (isset($param['status']) && $param['status']) {
            $query->where('goods.status', $param['status']);
        }

        $created_at = $param['created_at'] ?? '';
        if($created_at) {
            $created_at = explode('/', $created_at);
            $start_data = $created_at[0];
            $end_data = Carbon::parse($created_at[1])->addDay()->format('Y-m-d H:i:s');
            $query->where('goods.created_at', '>=', $start_data);
            $query->where('goods.created_at', '<=', $end_data);
        }

        $updated_at = $param['updated_at'] ?? '';
        if($updated_at) {
            $updated_at = explode('/', $updated_at);
            $start_data = $updated_at[0];
            $end_data = Carbon::parse($updated_at[1])->addDay()->format('Y-m-d H:i:s');
            $query->where('goods.updated_at', '>=', $start_data);
            $query->where('goods.updated_at', '<=', $end_data);
        }

        $shelf_at = $param['shelf_at'] ?? '';
        if($shelf_at) {
            $shelf_at = explode('/', $shelf_at);
            $start_data = $shelf_at[0];
            $end_data = Carbon::parse($shelf_at[1])->addDay()->format('Y-m-d H:i:s');
            $query->where('goods.shelf_at', '>=', $start_data);
            $query->where('goods.shelf_at', '<=', $end_data);
        }

        $query->orderByRaw('goods_classification.displayorder, goods.sort_num asc')->select('goods.*');
        if (isset($param['export']) && $param['export'] == 1) {
            return (clone $query)->get()->toArray();
        } else {
            $data = $query->paginate(10);
        }

        if (!empty($data)) {
            $classification = $this->assemblySaaSClassifyCation();
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
    function assemblySaaSClassifyCation()
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

        $lv1 = array(['id'=>0,'title'=>'请选择产品']);
        $lv2 = array();
        $lv3 = array();
        if ($data) {
            foreach ($data as $k => $v) {
                if ($v['lv'] == 1) {
                    $lv1[] = $v;
                }
            }
            foreach ($lv1 as $ks => $vs) {  //循环一级数组数据
                $lv2[$vs['title']][] = ['id'=>0,'title'=>'请选择平台'];
                $lv3[$vs['id']][][] = ['id'=>0,'title'=>'请选择功能套餐类型'];
                foreach ($data as $kb => $vb) {         //循环二级数组数据
                    $s = 0;
                    $a = 0;
                    if ($vb['lv'] == 2 && $vb['pid'] === $vs['id']) {
                        $lv2[$vs['title']][] = $vb;

                            $lv3[$vs['id']][$vb['id']][] = ['id'=>0,'title'=>'请选择功能套餐类型'];

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


    public function threeLevelLinkAgeSaas()
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

            $exists_sort = $this->existsSort($data['sort_num'], $data['id']);
            if($exists_sort){
                return 'repeat_sort';
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

            $exists_sort = $this->existsSort($data['sort_num']);
            if($exists_sort){
                return 'repeat_sort';
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
        $classification = $this->assemblySaaSClassifyCation();
        if($data['info']){
            $data['info'] = unserialize($data['info']);
        }
        $data['level1name'] = $classification[$data['level1']]['title'] ?? '';
        $data['level2name'] = $classification[$data['level2']]['title'] ?? '';
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
                    $value = $value == 1 ? '上架' : '下架';
                }
                $row[] = $value;
            }

            $rows[] = $row;
        }

        $userExport = new GoodsExport($rows);
        $fileName = 'SDK商品列表' . time() . '.xlsx';
        return \Excel::download($userExport, $fileName);
    }

    public function exportSaaS($list, $field)
    {
        $title_arr = [
            'id' => 'ID',
            'level1' => '套餐类型',
            'level2' => '文件档位',
            'price' => 'Pricing(USD)',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'shelf_at' => '上架时间',
        ];

        $classification = $this->assemblySaaSClassifyCation();

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
                if (in_array($key, ['level1', 'level2'])) {
                    $value = $classification[$value]['title'];
                } elseif ($key == 'status') {
                    $value = $value == 1 ? '上架' : '下架';
                }
                $row[] = $value;
            }

            $rows[] = $row;
        }

        $userExport = new GoodsExport($rows);
        $fileName = 'SaaS商品列表' . time() . '.xlsx';
        return \Excel::download($userExport, $fileName);
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

    /**
     * SaaS商品管理-排序是否存在
     * @param $sort
     * @param $id
     * @return bool
     */
    public function existsSort($sort, $id = ''){
        $query = Goods::query()->where('is_saas', 1)->where('sort_num', $sort)->where('deleted', 0);
        if($id){
            $query->where('id', '!=', $id);
        }

        return $query->exists();
    }

    /**
     * 获取目前最大排序
     * @return mixed
     */
    public function getMaxSort(){
        return Goods::query()->where('is_saas', 1)->where('deleted', 0)->orderByDesc('sort_num')->value('sort_num');
    }

    /**
     * 获取SaaS产品
     * @return array
     */
    public function getSaaSGoods(){
        //套餐
        $combos = Goodsclassification::getComboOrGear(1);
        //档位
        $gear = Goodsclassification::getComboOrGear(2);
        //商品
        $goods = Goods::getGoods();
        $goods = $goods->groupBy('level1')->toArray();

        $data = [];
        //循环套餐
        foreach ($combos as $combo){
            $result = ['combo' => $combo['title'], 'plan'=>[]];
            //获取套餐下产品
            $combo_goods = $goods[$combo['id']] ?? [];
            foreach ($combo_goods as $combo_good){
                //只返回排序前五的商品
                if(count($result['plan']) == 5){
                    break;
                }

                $level2 = $combo_good['level2'];
                $goods_gear = array_get($gear, "$level2.title");

                //不返回手动配置
                if($goods_gear == '手动配置'){
                    continue;
                }

                $price = $combo_good['price'];
                $result['plan'][] = ['id'=>$combo_good['id'], 'gear' => $goods_gear, 'price' => $price];
            }

            $data[] = $result;
        }
        return $data;
    }

    /**
     * 根据档位获取商品
     * @param $combo
     * @param $gear
     * @return mixed
     */
    public function getGoodsByGear($combo, $gear){
        return Goods::getGoodsByGear($combo, $gear);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function findById($id){
        return Goods::query()->where('id', $id)->first();
    }
}