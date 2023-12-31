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

use App\Models\Goodsclassification;
use App\Models\Goods;
use Auth;

class GoodsclassificationService
{
    public function __construct()
    {

    }

    public function getCategoricalData()
    {
        $Goodsclassification = new Goodsclassification();
        $where = "deleted=0 and is_saas=0";
        $field = "id,title,lv,pid,displayorder";
        $order = "displayorder,id desc";
        $list1 = $Goodsclassification->_where($where, $order, $field);
        if ($list1) {
            $data = $this->assemblyHtml($list1);
        } else {
            $data = '<div style="height: 300px; width: 100%; text-align: center; padding-top: 130px;"><div>暂无数据</div></div>';
        }
        return $data;
    }
    public function getsdkCategoricalData()
    {
        $Goodsclassification = new Goodsclassification();
        $where = "deleted=0 and is_saas=1";
        $field = "id,title,lv,pid,displayorder";
        $order = "displayorder,id desc";
        $list1 = $Goodsclassification->_where($where, $order, $field);
        if ($list1) {
            $data = $this->assemblysaasHtml($list1);
        } else {
            $data = '<div style="height: 300px; width: 100%; text-align: center; padding-top: 130px;"><div>暂无数据</div></div>';
        }
        return $data;
    }

    function assemblyHtml($data)
    {
        $html = '<ol class="dd-list">';
        foreach ($data as $k => $v) {
            if ($v['lv'] == 1) {
                $html .= '<li class="dd-item dd3-item item_' . $v['id'] . '" data-id="' . $v['id'] . '" id="classSecond_' . $v['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $v['title'] . '<span class=" numbid_' . $v['id'] . '">&nbsp;&nbsp;<font  style="font-size: 1em">排序</font>:[' . $v['displayorder'] . ']</span><div class="item_edt_del"><font class="open_' . $v['id'] . '">';
                $html .= '<a style="text-decoration: none" class="abutton cloros2" href="' . $this->headerurl() . '/admin/goodsclassification/creategoodsClassification/' . $v['id'] . '"><i class="fa fa-plus-circle "></i> 新增子分类</a><a style="text-decoration: none" class="edit_' . $v['id'] . ' abutton cloros3" href="' . $this->headerurl() . '/admin/goodsclassification/updategoodsClassification/' . $v['id'] . '"><i class="fa fa-edit"></i> 修改</a><a onclick="del(' . $v['id'] . ')" class="abutton cloros4" style="text-decoration: none"><i class="fa fa-trash-o fa-delete"></i> 删除</a></div></div>';
                $html .= $this->assemPage($v['id'], $data);
                $html .= '</li>';
            }
        }
        $html .= '</ol>';
        return $html;
    }

    function assemblysaasHtml($data)
    {
        $html = '<ol class="dd-list">';
        foreach ($data as $k => $v) {
            if ($v['lv'] == 1) {
                $html .= '<li class="dd-item dd3-item item_' . $v['id'] . '" data-id="' . $v['id'] . '" id="classSecond_' . $v['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $v['title'] . '<span class=" numbid_' . $v['id'] . '">&nbsp;&nbsp;<font  style="font-size: 1em">排序</font>:[' . $v['displayorder'] . ']</span><div class="item_edt_del"><font class="open_' . $v['id'] . '">';
                $html .= '<a style="text-decoration: none" class="abutton cloros2" href="' . $this->headerurl() . '/admin/goodsclassification/createsaasgoodsClassification/' . $v['id'] . '"><i class="fa fa-plus-circle "></i> 新增子分类</a><a style="text-decoration: none" class="edit_' . $v['id'] . ' abutton cloros3" href="' . $this->headerurl() . '/admin/goodsclassification/updatesaasgoodsClassification/' . $v['id'] . '"><i class="fa fa-edit"></i> 修改</a><a onclick="del(' . $v['id'] . ')" class="abutton cloros4" style="text-decoration: none"><i class="fa fa-trash-o fa-delete"></i> 删除</a></div></div>';
                $html .= $this->assemsaasPage($v['id'], $data);
                $html .= '</li>';
            }
        }
        $html .= '</ol>';
        return $html;
    }

    function assemPage($pid, $data, &$html = "")
    {
        $html .= '<ol class="dd-list">';
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid && $v['pid'] != 0) {
                if($v['lv']==2){
                    $html .= '<li class="dd-item dd3-item" data-id="' . $v['id'] . '" parentid="' . $v['pid'] . '" id="classSecond_' . $v['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $v['title'] . '<span class=" numbid_' . $v['id'] . '">&nbsp;&nbsp;排序:[' . $v['displayorder'] . ']</span><div class="item_edt_del"><font class="open_' . $v['id'] . '">';
                    $html .= '<a style="text-decoration: none" class="abutton cloros2" href="' . $this->headerurl() . '/admin/goodsclassification/creategoodsClassification/' . $v['id'] . '"><i class="fa fa-plus-circle "></i> 新增子分类</a><a style="text-decoration: none" class="edit_' . $v['id'] . ' abutton cloros3" href="' . $this->headerurl() . '/admin/goodsclassification/updategoodsClassification/' . $v['id'] . '"><i class="fa fa-edit"></i> 修改</a><a style="text-decoration: none" onclick="del(' . $v['id'] . ')" class="abutton cloros4"><i class="fa fa-trash-o fa-delete"></i> 删除</a></div></div></li>';
                    $html .= $this->assemPage($v['id'], $data);
                }else{
                    $html .= '<li class="dd-item dd3-item" data-id="' . $v['id'] . '" parentid="' . $v['pid'] . '" id="classSecond_' . $v['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $v['title'] . '<span class=" numbid_' . $v['id'] . '">&nbsp;&nbsp;排序:[' . $v['displayorder'] . ']</span><div class="item_edt_del"><font class="open_' . $v['id'] . '">';
                    $html .= '<a style="text-decoration: none" class="edit_' . $v['id'] . ' abutton cloros3" href="' . $this->headerurl() . '/admin/goodsclassification/updategoodsClassification/' . $v['id'] . '"><i class="fa fa-edit"></i> 修改</a><a style="text-decoration: none" onclick="del(' . $v['id'] . ')" class="abutton cloros4"><i class="fa fa-trash-o fa-delete"></i> 删除</a></div></div></li>';
                }
            }
        }
        $html .= '</ol>';
        return $html;
    }


    function assemsaasPage($pid, $data, &$html = "")
    {
        $html .= '<ol class="dd-list">';
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid && $v['pid'] != 0) {
                    $html .= '<li class="dd-item dd3-item" data-id="' . $v['id'] . '" parentid="' . $v['pid'] . '" id="classSecond_' . $v['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $v['title'] . '<span class=" numbid_' . $v['id'] . '">&nbsp;&nbsp;排序:[' . $v['displayorder'] . ']</span><div class="item_edt_del"><font class="open_' . $v['id'] . '">';
                    $html .= '<a style="text-decoration: none" class="edit_' . $v['id'] . ' abutton cloros3" href="' . $this->headerurl() . '/admin/goodsclassification/updatesaasgoodsClassification/' . $v['id'] . '"><i class="fa fa-edit"></i> 修改</a><a style="text-decoration: none" onclick="del(' . $v['id'] . ')" class="abutton cloros4"><i class="fa fa-trash-o fa-delete"></i> 删除</a></div></div></li>';
            }
        }
        $html .= '</ol>';
        return $html;
    }

    public function getCategorical()
    {
        $Goodsclassification = new Goodsclassification();
        $where = "deleted = 0 and lv < 3 and is_saas=0";
        $field = "id,title,lv,pid,displayorder";
        $order = "displayorder,id desc";
        $material = $Goodsclassification->_where($where, $order, $field);
        $arr_project = $this->menuLeft($material);
        return $arr_project ?? [];
    }

    public function getsaasCategorical()
    {
        $Goodsclassification = new Goodsclassification();
        $where = "deleted = 0 and lv < 3 and is_saas=1";
        $field = "id,title,lv,pid,displayorder";
        $order = "displayorder,id desc";
        $material = $Goodsclassification->_where($where, $order, $field);
        $arr_project = $this->menuLeft($material);
        return $arr_project ?? [];
    }

    public function getSaaSCombo(){
        return Goodsclassification::query()
            ->where('deleted', 0)
            ->where('lv', 1)
            ->where('is_saas', 1)
            ->orderByRaw('displayorder,id desc')
            ->select(['id', 'title'])
            ->get()
            ->toArray();
    }


    function menuLeft($menu, $id_field = 'id', $pid_field = 'pid', $lefthtml = '─', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($menu as $v) {
            if ($v[$pid_field] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin;
                $v['lefthtml'] = '├' . str_repeat($lefthtml, $lvl);
                $arr[] = $v;
                $arr = array_merge($arr, $this->menuLeft($menu, $id_field, $pid_field, $lefthtml, $v[$id_field], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }


    public function getFindcategorical($id)
    {
        $Goodsclassification = new Goodsclassification();
        $where = "deleted=0 and id='$id'";
        $data = $Goodsclassification->_find($where);
        $data = $Goodsclassification->objToArr($data);
        return $data;
    }


    /**
     * @param $param
     * @remark 增/删改/分类
     * @return int|string
     * @throws \think\db\exception\DbException
     */
    public function addEditcaregorical($param)
    {
        $Goodsclassification = new Goodsclassification();
        if (isset($param['data'])) {
            $data = $param['data'];
        }
        if(isset($data['pid']) && $data['pid']){
            $topdata = $Goodsclassification->_find("id=".$data['pid']." and deleted=0 and is_saas=0");
            $topdata = $Goodsclassification->objToArr($topdata);
            $lv=$topdata['lv']+1;
        }else{
            $lv=1;
        }
        $data['lv'] = $lv;
        if (isset($data['id'])) {
            $where = "id='{$data['id']}' and is_saas=0";
            $is_find = $Goodsclassification->_find($where);
            $is_find = $Goodsclassification->objToArr($is_find);
            if ($is_find['title'] != $data['title']) {
                $names = $Goodsclassification->_find("title='" . $data['title'] . "' and pid='{$is_find['pid']}' and deleted=0 and is_saas=0");
                $names = $Goodsclassification->objToArr($names);
                if ((isset($names) && $names)) {
                    return "repeat";
                }
            }
            $bool = $Goodsclassification->_update($data, $where);
        } elseif (isset($param['delid'])) {
            $ids = $this->getIds($param['delid']);
            $ids = implode(',', $ids);
            $goods=new goods();
            $is_find=$goods->_find("(level1 in('{$ids}') or level2 in('{$ids}') or level3 in('{$ids}')) and deleted=0 and is_saas=0");
            $is_find=$goods->objToArr($is_find);
            if($is_find)return 'isdata';
            $bool = $Goodsclassification->_update(['deleted' => 1], "id=" . $param['delid']);
            if ($bool) {
                $Goodsclassification->_update(['deleted' => 1], "pid=" . $param['delid']);
            }
        } else {
            $names = $Goodsclassification->_find("title='" . $data['title'] . "' and pid={$data['pid']} and deleted=0 and is_saas=0");
            $names = $Goodsclassification->objToArr($names);
            if (isset($names) && $names) {
                return "repeat";
            }
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $bool = $Goodsclassification->_insert($data);
        }
        return $bool;
    }

    public function addsaasEditcaregorical($param)
    {
        $Goodsclassification = new Goodsclassification();
        if (isset($param['data'])) {
            $data = $param['data'];
        }
        if(isset($data['pid']) && $data['pid']){
            $topdata = $Goodsclassification->_find("id=".$data['pid']." and deleted=0 and is_saas=1");
            $topdata = $Goodsclassification->objToArr($topdata);
            $lv=$topdata['lv']+1;
        }else{
            $lv=1;
        }
        $data['lv'] = $lv;
        if (isset($data['id'])) {
            $where = "id='{$data['id']}' and is_saas=1";
            $is_find = $Goodsclassification->_find($where);
            $is_find = $Goodsclassification->objToArr($is_find);
            if ($is_find['title'] != $data['title']) {
                $names = $Goodsclassification->_find("title='" . $data['title'] . "' and pid='{$is_find['pid']}' and deleted=0 and is_saas=1 and id != {$is_find['id']}");
                $names = $Goodsclassification->objToArr($names);
                if ((isset($names) && $names)) {
                    return "repeat";
                }
            }
            $bool = $Goodsclassification->_update($data, $where);
        } elseif (isset($param['delid'])) {
            $ids = $this->getIds($param['delid']);
            $ids = implode(',', $ids);
            $goods=new goods();
            $is_find=$goods->_find("(level1 in('{$ids}') or level2 in('{$ids}') or level3 in('{$ids}')) and deleted=0 and is_saas=1");
            $is_find=$goods->objToArr($is_find);
            if($is_find)return 'isdata';
            $bool = $Goodsclassification->_update(['deleted' => 1], "id=" . $param['delid']);
            if ($bool) {
                $Goodsclassification->_update(['deleted' => 1], "pid=" . $param['delid']);
            }
        } else {
            $names = $Goodsclassification->_find("title='" . $data['title'] . "' and pid={$data['pid']} and deleted=0 and is_saas=1");
            $names = $Goodsclassification->objToArr($names);
            if (isset($names) && $names) {
                return "repeat";
            }
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['updated_at'] = date("Y-m-d H:i:s");
            $data['is_saas'] = 1;
            $bool = $Goodsclassification->_insert($data);
        }
        return $bool;
    }

    function headerurl()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type . $_SERVER['HTTP_HOST'];
    }


    function getIds($id, &$arr = array())
    {
        array_push($arr, $id);
        $operate = new Goodsclassification();
        $where="deleted=0 and pid=".$id;
        $lower_ids = $operate->_where($where,"lv", "id");
        if ($lower_ids) {
            foreach ($lower_ids as $key => $val) {
                $this->getIds($val['id'], $arr);
            }
        }
        return $arr;
    }

    public static function getNameById($id){
        $good_class = Goodsclassification::find($id);
        return $good_class->title;
    }

}