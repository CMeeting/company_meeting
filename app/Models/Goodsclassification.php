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
            ->select(['id', 'title', 'pid'])
            ->orderBy('displayorder')
            ->get();

        if($level == 1){
            return $result->toArray();
        }else{
            return $result->keyBy('id')->toArray();
        }
    }

    /**
     * 获取套餐下的档位
     * @return array
     */
    public static function getGearGroupByCombo(){
        $result = Goodsclassification::query()
            ->where('is_saas', 1)
            ->where('deleted', 0)
            ->where('lv', 2)
            ->select(['id', 'title', 'pid'])
            ->orderBy('displayorder')
            ->get();

        return $result->groupBy('pid')->toArray();
    }

    /**
     * 获取索引为id的数组
     * @return array
     */
    public static function getKeyById(){
        $set = Goodsclassification::query()
            ->where('is_saas', Goods::IS_SAAS_1_YES)
            ->where('deleted', Goods::DELETE_0_NO)
            ->get();

        return $set->keyBy('id')->toArray();
    }

    /**
     * 获取套餐
     * @param $id
     * @return mixed
     */
    public static function getComboById($id){
        return Goodsclassification::query()
            ->where('id', $id)
            ->value('title');
    }
}