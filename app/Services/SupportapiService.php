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

use App\Models\SupportLog;
use Auth;
use App\Models\Support;
use App\Models\Mailmagicboard;
use App\Models\DocumentationModel as PlatformVersion;
use Illuminate\Support\Facades\DB;

class SupportapiService
{
    public function __construct()
    {

    }

    public function get_data($data){
        $supprot=new Support();
        $supportlog=new SupportLog();
        $info=$supprot->_find("order_no='".$data['order_no']."' and is_delete=0");
        $info=$supprot->objToArr($info);
        if(!$info){
            return ['code'=>'403','msg'=>"没有找到该数据"];
        }
        $list=$supportlog->_where("order_no='{$data['order_no']}'","created_at desc");
        $arr['info']=$info;
        $arr['list']=$list;
        return ['code'=>'200','msg'=>"ok",'data'=>$arr];
    }
}