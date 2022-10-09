<?php

namespace App\Services;


use App\Models\base\PlatformVersion;
use Illuminate\Support\Facades\DB;

class ApiChangeLogsService
{

    public function getChangeLogs($platform, $product)
    {
        $platform_ojb = new PlatformVersion();
        $platform_array = $platform_ojb->_find("name = '{$platform}' and lv = 1 and deleted = 0");
        $platform_array = $platform_ojb->objToArr($platform_array);
        $product_array = $platform_ojb->selects("name = '{$product}' and lv = 2 and deleted = 0", "id");
        $product_array = array_column($product_array, "id");
        if (!$platform_array || !$product_array) {
            return ['code' => '403', 'msg' => '没有找到该数据'];
        }
        $where['platform'] = $platform_array['id'];
        $list = DB::table('change_logs')
            ->where($where)
            ->whereIn("product", $product_array)
            ->select("version_no", "content", "platform", "change_date")
            ->orderBy("change_date", "desc")
            ->get();
        $data = [];
        if ($list) {
            //构建栅格版本信息
            foreach (obj_to_arr($list) as $v_key => $val) {
//                $val['content'] = htmlspecialchars($val['content']);
                $val['change_date'] = date("Y-m-d", strtotime($val['change_date']));
                $data['Version ' . explode('.', $val['version_no'])[0]]['v' . $val['version_no']] = $val;
            }
            foreach ($data as $k => $v) {
                foreach ($v as $k1 => $v2) {
                    $ver = $k1;
                    break;
                }
                $result['new_version']['version'] = $k;
                $result['new_version']['version_to_v'] = $ver;
                break;
            }
        }
        $result['data'] = $data;
        $result['category'] = $this->getAllCategory($product_array);
        return $result;
    }

    public function getAllCategory($product_array)
    {
        $products = DB::table("change_logs as c")
            ->leftJoin("platform_version as p", "p.id", "=", "c.product")
            ->where("c.is_delete", 0)
            ->where("p.lv", 2)
            ->where("p.deleted", 0)
            ->select("p.name")
            ->groupBy("p.name")
            ->get();
        $products = array_column(obj_to_arr($products), "name");

        $platforms = DB::table("change_logs as c")
            ->leftJoin("platform_version as p", "p.id", "=", "c.platform")
            ->whereIn("c.product", $product_array)
            ->where("c.is_delete", 0)
            ->where("p.lv", 1)
            ->where("p.deleted", 0)
            ->select("p.name")
            ->groupBy("p.name")
            ->get();
        $platforms = array_column(obj_to_arr($platforms), "name");

        return ['products' => $products, "platforms" => $platforms];
    }


}