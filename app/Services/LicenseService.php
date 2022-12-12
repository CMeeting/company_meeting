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
use Maatwebsite\Excel\Facades\Excel;

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
        $fileName = '授权码列表' . time() . '.xlsx';
        return Excel::download($userExport, $fileName);
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
        foreach ($goods_data as $ks => $vs) {
            if ($data['level1'] == $vs['level1'] && $data['level2'] == $vs['level2'] && $data['level3'] == $vs['level3']) {
                $goodsid = $vs['id'];
            }
        }

        if(!isset($goodsid))return ['code' => 500, 'msg' => $classification[$data['level1']]['title'].'-'.$classification[$data['level2']]['title'].'-'.$classification[$data['level3']]['title'].'下没有商品'];

        $licensecodedata=LicenseService::buildLicenseCodeData(0, 1, $data['user_id'], $data['level1'], $data['level2'], $data['level3'],  $data["appid"], $data['email'],0,0);
        foreach ($licensecodedata as $k=>$v){
            $licensecodedata[$k]['user_email'] = $data['email'];
            $licensecodedata[$k]['lise_type'] = 1;
        }
        $res=$lisecosdmode->_insert($licensecodedata);
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

    /**
     * 构造序列码数组
     * @param $ordergoods_no
     * @param $period
     * @param $user_id
     * @param $product_id
     * @param $platform_id
     * @param $licensetype_id
     * @param $app_id
     * @param $email
     * @return array
     * @throws \Exception
     */
    public static function buildLicenseCodeData($ordergoods_no, $period, $user_id, $product_id, $platform_id, $licensetype_id, $app_id, $email,$order_id,$ordergoods_id){
        $license_code_arr = [];

        $start_time = time();
        $end_time = strtotime("+" . $period . " year");

        $product = GoodsclassificationService::getNameById($product_id);
        $platform = GoodsclassificationService::getNameById($platform_id);
        $license_type = GoodsclassificationService::getNameById($licensetype_id);

        $platform_name=$product ." for ". $platform ." (". $license_type.")";
        $generateService = new GenerateLicenseCodeService();
        if($product == 'ComPDFKit SDK'){
            $license_code_pdf = $generateService->generate('ComPDFKit PDF SDK ', $platform, $license_type, $start_time, $end_time, $app_id, $email);
            $license_code_arr[] = self::getLicenseCodeData($license_code_pdf, $ordergoods_no, $user_id, $product_id, $platform_id, $licensetype_id, $app_id, $period,$order_id,$ordergoods_id,'ComPDFKit PDF SDK ');

            $license_code_conversion = $generateService->generate('ComPDFKit Conversion SDK ', $platform, $license_type, $start_time, $end_time, $app_id, $email);
            $license_code_arr[] = self::getLicenseCodeData($license_code_conversion, $ordergoods_no, $user_id, $product_id, $platform_id, $licensetype_id, $app_id, $period,$order_id,$ordergoods_id,'ComPDFKit Conversion SDK ');
        }else{
            $license_code_conversion = $generateService->generate($product, $platform, $license_type, $start_time, $end_time, $app_id, $email);
            $license_code_arr[] = self::getLicenseCodeData($license_code_conversion, $ordergoods_no, $user_id, $product_id, $platform_id, $licensetype_id, $app_id, $period,$order_id,$ordergoods_id,$platform_name);
        }

        return $license_code_arr;
    }

    /**
     * 返回序列码数组
     * @param $license_code
     * @param $ordergoods_no
     * @param $user_id
     * @param $product_id
     * @param $platform_id
     * @param $licensetype_id
     * @param $app_id
     * @param $period
     * @return array
     */
    public static function getLicenseCodeData($license_code, $ordergoods_no, $user_id, $product_id, $platform_id, $licensetype_id, $app_id, $period,$order_id,$ordergoods_id,$platform_name=''){
        $license_key = $license_code['key'];
        $license_secret = $license_code['secret'];
        return [
            'order_id' => $order_id,
            'ordergoods_id' => $ordergoods_id,
            'goods_no' => $ordergoods_no,
            'user_id' => $user_id,
            'products_id' => $product_id,
            'platform_id' => $platform_id,
            'licensetype_id' => $licensetype_id,
            'license_key' => $license_key,
            'license_secret' => $license_secret,
            'uuid' => implode(',', $app_id),
            'period' => $period,
            'type' => 2,
            'status' => 1,
            'expire_time' => date("Y-m-d H:i:s", strtotime("+" . $period . " year")),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'platform_name'=>$platform_name
        ];
    }
}