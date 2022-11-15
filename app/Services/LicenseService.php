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
            } elseif ($param['query_type'] == "uuid") {
                $where .= " and l.uuid like '%" . $param['info'] . "%'";
            } elseif ($param['query_type'] == "email") {
                $where .= " and u.email like '%" . $param['info'] . "%'";
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
        $data = $query->select("l.id", "o.order_id", "o.id as order_no", "l.uuid", "l.created_at", "l.expire_time",
            "u.email", "l.license_key", "l.license_key_url", "l.type", "l.status", "l.products_id", "l.platform_id", "l.licensetype_id")
            ->whereRaw($where)
            ->leftJoin("orders_goods as o","l.order_id", "=", "o.order_id")
            ->leftJoin("users as u","u.id", "=", "l.user_id")
            ->orderBy("l.created_at","desc")
            ->paginate(10);
        $goodsClassifications = $this->getGoodsClassifications();
        if ($data) {
            foreach ($data as $key => $value) {
                $name = $goodsClassifications[$value->products_id] . " for " . $goodsClassifications[$value->platform_id] . " ( " . $goodsClassifications[$value->licensetype_id] . " ) ";
                $data[$key]->named = cut_str($name,6);
                $data[$key]->name = $name;
                $data[$key]->order_id = $value->order_id ?? '-';
                $data[$key]->order_no = $value->order_no ?? '-';
                $data[$key]->license_keyd = cut_str($value->license_key,5);
                $data[$key]->uuidd = cut_str($value->uuid,5);
                $data[$key]->emaild = cut_str($value->email,5);
                $data[$key]->type = config("constants.license_type")[$value->type];
                $data[$key]->statusd = config("constants.license_status")[$value->status];
            }
        }
        return $data ?? [];
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
        $info = $query->select("l.id","l.license_secret", "o.order_id", "o.id as order_no", "l.uuid", "l.created_at", "l.expire_time",
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
        $lisecosdmode=new LicenseModel();
        $is_user = $user->existsEmail($data['email']);
        if (!$is_user) {
            $arr['full_name'] = $data['email'];
            $arr['email'] = $data['email'];
            $arr['flag'] = 3;
            $arr['type'] = 4;
            $arr['created_at'] = date("Y-m-d H:i:s");
            $arr['updated_at'] = date("Y-m-d H:i:s");
            $user_id = Db::table("users")->insertGetId($arr);
        } else {
            $users = DB::table('users')->where('email', $data['email'])->first();
            $user_id = $users->id;
        }
       $lisecosd = str_pad("'".mt_rand(1,9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT);
        $license_secret = str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT)."-".str_pad("'".mt_rand(1, 9999)."'", 4, '0', STR_PAD_LEFT);

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
         return $res;

    }





}