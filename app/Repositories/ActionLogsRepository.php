<?php
/**
 * YICMS
 * ============================================================================
 * 版权所有 2014-2017 YICMS，并保留所有权利。
 * 网站地址: http://www.yicms.vip
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Created by PhpStorm.
 * Author: kenuo
 * Date: 2017/11/17
 * Time: 下午4:40
 */

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\ActionLog;

class ActionLogsRepository
{
    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return ActionLog::create($data);
    }

    /**
     * 获取全部的操作日志
     * @return mixed
     */
    public function getWithAdminActionLogs($datapar)
    {

        if(isset($datapar['info'])&&$datapar['info']){
            $admin=DB::table("admins")->whereRaw("name='{$datapar['info']}'")->first();
            if($admin){
                $admin_id=$admin->id;
            }else{
                $admin_id=0;
            }
            $data=ActionLog::with('admin')->whereRaw("admin_id=$admin_id")->latest('created_at')->paginate(20);
        }else{
            $data=ActionLog::with('admin')->latest('created_at')->paginate(20);
        }

        foreach ($data as $k=>$value){
            $data[$k]->info=$this->cut_str($value->data['action'], 40);
        }
        return $data;
    }

    function cut_str($sourcestr, $cutlength)
    {
        $returnstr = '';
        $i = $n = 0;
        $str_length = strlen($sourcestr);
        while (($n < $cutlength) and ($i <= $str_length)) {
            $temp_str = substr($sourcestr, $i, 1);
            $ascnum = Ord($temp_str);
            if ($ascnum >= 224) {
                $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                $i = $i + 3;
                $n++;
            } elseif ($ascnum >= 192) {
                $returnstr = $returnstr . substr($sourcestr, $i, 2);
                $i = $i + 2;
                $n++;
            } elseif ($ascnum >= 65 && $ascnum <= 90) {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;
                $n++;
            } else {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;
                $n = $n + 0.5;
            }
        }
        if ($str_length > $i) {
            $returnstr = $returnstr . "…";
        }
        return $returnstr;
    }
}