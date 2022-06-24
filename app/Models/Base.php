<?php

namespace App\Models;

use App\Models\Traits\RbacCheck;
use Illuminate\Support\Facades\DB;

class Base{
    public static $table = '';

    /**
     * @return void
     */
    public static function select($condition, $field = '*', $order ="id desc")
    {
        return Db::table(static::$table)
            ->select(DB::raw($field))
            ->where($condition)
            ->orderByRaw($order)
            ->get();
    }

    public static function selectLimit($condition, $field = '*', $order = 'id DESC',$limit = 10)
    {
        return Db::table(static::$table)
            ->select(DB::raw($field))
            ->where($condition)
            ->orderByRaw($order)
            ->limit($limit)
            ->get();
    }

    public static function find($condition, $filter_str = '', $order = 'created_at DESC') {
        return Db::table(static::$table)
            ->where($condition)
            ->where($filter_str)
            ->orderByRaw($order)
            ->get();
    }

    public static function insertGetId(array $data)
    {
        return Db::table(static::$table)
            ->insertGetId($data);
    }

    public static function update(array $data, $where)
    {
        return Db::table(static::$table)
            ->where($where)
            ->update($data);
    }

    /**
     * @param $where
     * 删除数据方法
     * @return mixed
     */
    public static function delete($where)
    {
        return Db::table(static::$table)
            ->where($where)
            ->delete();
    }

    public function objToArr($object) {

        //先编码成json字符串，再解码成数组

        return json_decode(json_encode($object), true);

    }
}
