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
use App\Models\LicenseModel;
use App\Models\Mailmagicboard;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class LicenseService
{

    public function __construct()
    {

    }

    public function list($param)
    {
        $where = " 1=1 ";
        if ($param['info']) {
            if ($param['query_type'] == "order_no") {
                $where .= " and o.order_no like '%" . $param['info'] . "%'";
            } elseif ($param['query_type'] == "goods_no") {
                $where .= " and o.goods_no like '%" . $param['info'] . "%'";
            }elseif ($param['query_type'] == "uuid") {
                $where .= " and l.uuid like '%" . $param['info'] . "%'";
            } elseif ($param['query_type'] == "email") {
                $where .= " and u.email ='{$param['info']}'";
            }
        }
        if ($param['type']) {
            $where .= " and l.type = " . $param['type'];
        }
        if ($param['level1']) {
            $where .= " and l.products_id = " . $param['level1'];
        }
        if ($param['level2']) {
            $where .= " and l.platform_id = " . $param['level2'];
        }
        if ($param['level3']) {
            $where .= " and l.licensetype_id = " . $param['level3'];
        }
        if (isset($param['created_start']) && $param['created_start'] && isset($param['created_end']) && $param['created_end']) {
            $where .= " AND l.created_at BETWEEN '" . $param['created_start'] . "' AND '" . $param['created_end'] . "'";
        } elseif (isset($param['created_start']) && $param['created_start'] && empty($param['created_end'])) {
            $where .= " AND l.created_at >= '" . $param['created_start'] . "'";
        } elseif (isset($param['created_end']) && $param['created_end'] && empty($param['created_start'])) {
            $where .= " AND l.created_at <= '" . $param['created_end'] . "'";
        }

        if (isset($param['expire_start']) && $param['expire_start'] && isset($param['expire_end']) && $param['expire_end']) {
            $where .= " AND l.expire_time BETWEEN '" . $param['expire_start'] . "' AND '" . $param['expire_end'] . "'";
        } elseif (isset($param['expire_start']) && $param['expire_start'] && empty($param['expire_end'])) {
            $where .= " AND l.expire_time >= '" . $param['expire_start'] . "'";
        } elseif (isset($param['expire_end']) && $param['expire_end'] && empty($param['expire_start'])) {
            $where .= " AND l.expire_time <= '" . $param['expire_end'] . "'";
        }
        $query = DB::table("license_code as l");
        if ($param['export'] == 1) {
            $data = $query->select("l.id", "o.order_no as order_id", "o.goods_no as order_no", "l.uuid", "l.created_at", "l.expire_time",
                "u.email", "l.license_key", "l.license_key_url", "l.type", "l.status", "l.products_id", "l.platform_id", "l.licensetype_id")
                ->whereRaw($where)
                ->leftJoin("orders_goods as o","l.ordergoods_id", "=", "o.id")
                ->leftJoin("users as u","u.id", "=", "l.user_id")
                ->orderBy("l.id","desc")
                ->get()->toArray();
        }else{
            $data = $query->select("l.id", "o.order_no as order_id", "o.goods_no as order_no", "l.uuid", "l.created_at", "l.expire_time",
                "u.email", "l.license_key", "l.license_key_url", "l.type", "l.status", "l.products_id", "l.platform_id", "l.licensetype_id")
                ->whereRaw($where)
                ->leftJoin("orders_goods as o","l.ordergoods_id", "=", "o.id")
                ->leftJoin("users as u","u.id", "=", "l.user_id")
                ->orderBy("l.id","desc")
                ->paginate(10);

        }
        $goodsClassifications = $this->getGoodsClassifications();
        if ($data) {
            if($param['export'] != 1) {
                foreach ($data as $key => $value) {
                    $name = $goodsClassifications[$value->products_id] . " for " . $goodsClassifications[$value->platform_id] . " ( " . $goodsClassifications[$value->licensetype_id] . " ) ";
                    $data[$key]->named = cut_str($name, 6);
                    $data[$key]->name = $name;
                    $data[$key]->order_id = $value->order_id ?? '-';
                    $data[$key]->order_no = $value->order_no ?? '-';
                    $data[$key]->license_keyd = cut_str($value->license_key, 5);
                    $data[$key]->uuidd = cut_str($value->uuid, 5);
                    $data[$key]->emaild = cut_str($value->email, 5);
                    $data[$key]->type = config("constants.license_type")[$value->type];
                    $data[$key]->statusd = config("constants.license_status")[$value->status];
                }
            }else{
                foreach ($data as $key => $value) {
                    $data[$key]->order_id = $value->order_id ?? '-';
                    $data[$key]->order_no = $value->order_no ?? '-';
                    $name = $goodsClassifications[$value->products_id] . " for " . $goodsClassifications[$value->platform_id] . " ( " . $goodsClassifications[$value->licensetype_id] . " ) ";
                    $data[$key]->name = $name;
                }
            }
        }

            return $data ?? [];

    }

    public function export($list, $field)
    {
        $title_arr = [
            'order_id' => '总订单ID',
            'order_no' => '子订单ID',
            'email' => '用户账号',
            'name' => '商品名称',
            'uuid' => 'App ID/Machine ID',
            'created_at' => '创建时间',
            'expire_time' => '过期时间',
            'license_key' => 'license_key',
            'type' => '授权码类型',
            'status' => '状态',
        ];


        $field = explode(',', $field);

        $header = [];
        foreach ($field as $title) {
            $header[] = array_get($title_arr, $title);
        }
        $rows[] = $header;

        foreach ($list as $data) {
            $data=json_decode(json_encode($data), true);
            $row = [];
            foreach ($field as $key) {
                $value = array_get($data, $key);
                if ($key == 'type') {
                    switch ($value){
                        case 1:
                            $value ="sdk试用";
                            break;
                        case 2:
                            $value ="sdk";
                            break;
                    }
                }
                if ($key == 'status') {
                    switch ($value){
                        case 1:
                            $value ="正常";
                            break;
                        case 2:
                            $value ="停用";
                            break;
                        case 3:
                            $value ="过期";
                            break;
                    }
                }
                $row[] = $value;
            }

            $rows[] = $row;
        }

        $userExport = new GoodsExport($rows);
        $fileName = 'export' . DIRECTORY_SEPARATOR . '授权码列表' . time() . '.xlsx';
        \Excel::store($userExport, $fileName);

        //ajax请求 需要返回下载地址，在使用location.href请求下载地址
        return ['url' => route('download', ['file_name' => $fileName])];
    }

    public function getGoodsClassifications($param = [])
    {
        $where = " 1=1 ";
        if (isset($param['lv']) && $param['lv']) {
            $where .= " and lv = " . $param['lv'];
        }
        $data = DB::table("goods_classification")
            ->select("id", "title")
            ->whereRaw($where)
            ->get();
        return two_to_one(obj_to_arr($data), "id", "title");
    }

    public function changeStatus($param)
    {
        return DB::table("license_code")
            ->where("id", $param['id'])
            ->update(["status" => $param['status']]);
    }

    public function getInfo($id)
    {
        $query = DB::table("license_code as l");
        $info = $query->select("l.id","l.license_secret", "o.order_no as order_id", "o.goods_no as order_no", "l.uuid", "l.created_at", "l.expire_time",
            "u.email", "l.license_key", "l.license_key_url", "l.type", "l.status", "l.products_id", "l.platform_id", "l.licensetype_id")
            ->leftJoin("orders_goods as o", "l.order_id", "=", "o.order_id")
            ->leftJoin("users as u", "u.id", "=", "l.user_id")
            ->where("l.id", $id)
            ->get();
        $info = obj_to_arr($info)[0];
        $goodsClassifications = $this->getGoodsClassifications();
        $info['order_id'] = $info['order_id'] ?? '-';
        $info['order_no'] = $info['order_no'] ?? '-';
        $info['name'] = $goodsClassifications[$info['products_id']] . " for " . $goodsClassifications[$info['platform_id']] . " ( " . $goodsClassifications[$info['licensetype_id']] . " ) ";
        $info['type'] = config("constants.license_type")[$info['type']];
        $info['status'] = config("constants.license_status")[$info['status']];
        return $info;
    }


    public function createlicense($data){
        $user = new User();
        $goods = new Goods();
        $lisecosdmode=new LicenseModel();
        $goods_data = $goods->_where("deleted=0 and status=1");
        $classification = $this->assembly_orderclassification();
        $is_user = $user->existsEmail($data['email']);
        if (!$is_user) {
            $password = User::getRandStr();
            $arr['full_name'] = $data['email'];
            $arr['email'] = $data['email'];
            $arr['password'] = User::encryptPassword($password);
            $arr['flag'] = 3;
            $arr['type'] = 4;
            $arr['created_at'] = date("Y-m-d H:i:s");
            $arr['updated_at'] = date("Y-m-d H:i:s");
            $user_id = Db::table("users")->insertGetId($arr);
            //发送邮件
            $emailService = new EmailService();
            $emailModel = Mailmagicboard::getByName('后台新增订单（用户注册成功邮件）');
            $data['title'] = $emailModel->title;
            $data['info'] = $emailModel->info;
            $data['info'] = str_replace("#@username", $arr['full_name'], $data['info']);
            $data['info'] = str_replace("#@mail", $arr['email'], $data['info']);
            $data['info'] = str_replace("#@password", $password, $data['info']);
            $emailService->sendDiyContactEmail($data, 0, $arr['email']);
        } else {
            $users = DB::table('users')->where('email', $data['email'])->first();
            $user_id = $users->id;
        }
       $lisecosd = str_pad("'".mt_rand(1,9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT);
        $license_secret = str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT);
        foreach ($goods_data as $ks => $vs) {
            if ($data['level1'] == $vs['level1'] && $data['level2'] == $vs['level2'] && $data['level3'] == $vs['level3']) {
                $goodsid = $vs['id'];
            }
        }

        if(!isset($goodsid))return ['code' => 500, 'msg' => $classification[$data['level1']]['title'].'-'.$classification[$data['level2']]['title'].'-'.$classification[$data['level3']]['title'].'下没有商品'];
        $data=[
            'user_id'=>$user_id,
            'products_id'=>$data['level1'],
            'platform_id'=>$data['level2'],
            'licensetype_id'=>$data['level3'],
            'license_key'=>$lisecosd,
            'license_secret'=>$license_secret,
            'uuid'=>implode(',', $data["appid"]),
            'period'=>$data['period'],
            'type'=>2,
            'status'=>1,
            'expire_time'=>date("Y-m-d H:i:s",strtotime("+".$data['period']." year"))
            ];
         $res=$lisecosdmode->insertGetId($data);
         return ['code' => 1, 'msg' => "添加成功"];

    }


    function assembly_orderclassification()
    {
        $Goodsclassification = new Goodsclassification();
        $data = $Goodsclassification->_where("1=1");
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$v['id']] = $v;
        }
        return $arr;
    }


}