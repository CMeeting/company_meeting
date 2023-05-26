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
 */

class Goods extends Model
{
    protected $table = 'goods';

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
}