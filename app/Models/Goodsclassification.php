<?php
/**
 * @Created by PhpStorm 2021
 * @Author: Rengar
 * @Date: 2022/6/21
 * @Time: 14:42
 * @By The Way: Everyone here is talented and speaks well. I love being here!!!
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Goodsclassification extends Model
{
    protected $table = 'goods_classification';

    /**
     * 获取套餐或者档位
     * @param $level
     * @return array
     */
    public static function getComboOrGear($level){
        $result = Goodsclassification::query()
            ->where('is_saas', 1)
            ->where('deleted', 0)
            ->where('lv', $level)
            ->select(['id', 'title'])
            ->orderBy('displayorder')
            ->get();

        if($level == 1){
            return $result->toArray();
        }else{
            return $result->keyBy('id')->toArray();
        }
    }
}