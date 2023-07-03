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

/**
 * Class Goods
 * @package App\Models
 *
 * @property    $id
 * @property    $level1
 * @property    $level2
 * @property    $level3
 * @property    $status
 * @property    $price
 * @property    $deleted
 * @property    $created_at
 * @property    $updated_at
 * @property    $shelf_at
 * @property    $info
 * @property    $is_saas
 * @property    $sort_num
 */

class Goods extends Model
{
    protected $table = 'goods';

    const STATUS_0_INACTIVE = 0;
    const STATUS_1_ACTIVE = 1;

    const DELETE_0_NO = 0;
    const DELETE_1_YES = 1;

    const IS_SAAS_0_NO = 0;
    const IS_SAAS_1_YES = 1;

    const COMBO_MONTHLY = 'Monthly';
    const COMBO_ANNUALLY = 'Annually';
    const COMBO_PACKAGE = 'Package';

    public static function getGoods(){
        return Goods::query()
            ->where('is_saas', 1)
            ->where('deleted', 0)
            ->where('status', 1)
            ->select(['id', 'level1', 'level2', 'price'])
            ->orderBy('sort_num')
            ->get();
    }

    /**
     * 根据套餐和档位获取商品
     * @param $combo
     * @param $gear
     * @return mixed
     */
    public static function getGoodsByGear($combo, $gear){
        return Goods::query()
            ->where('is_saas', 1)
            ->where('deleted', 0)
            ->where('status', 1)
            ->where('level1', $combo)
            ->where('level2', $gear)
            ->first();
    }

    /**
     * 获取商品的可用文件数量
     * @param $goods_id
     * @return mixed
     */
    public static function getTotalFilesByGoods($goods_id){
        return Goods::query()
            ->leftJoin('goods_classification', 'goods.level2', '=', 'goods_classification.id')
            ->where('goods.id', $goods_id)
            ->value('title');
    }
}