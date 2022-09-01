<?php

namespace App\Services;


use App\Models\base\PlatformVersion;
use Illuminate\Support\Facades\DB;

class ApiChangeLogsService
{

    public function getChangeLogs($platform)
    {
        $platform_ojb = new PlatformVersion();
        $platform_array = $platform_ojb->_find("name like '{$platform}' and lv=1 and deleted=0");
        $platform_array = $platform_ojb->objToArr($platform_array);
        if(!$platform_array)return['code'=>'403','msg'=>'没有找到该数据'];
        $where['platform'] = $platform_array['id'];
        $list = DB::table('change_logs')
            ->where($where)
            ->select("version_no","content","platform","change_date")
            ->orderBy("change_date","desc")
            ->get();
        $data = [];
        if($list){
            //构建栅格版本信息
            foreach (obj_to_arr($list) as $v_key => $val) {
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
        return $result;
    }



}