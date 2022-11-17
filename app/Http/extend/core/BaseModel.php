<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2018/7/28
 * Time: 9:05
 */

namespace core;


use app\api\service\DevicesService;
use core\helper\ArrayHelper;
use core\helper\LogHelper;
use core\helper\StringHepler;
use think\Db;
use think\Exception;

class BaseModel
{
    public static $table = '';

    public static $insertColumns = [];

    public static function getTable()
    {
        return static::$table;
    }

    public static function getFieldsStr($diffFields = []) {
        return array_diff(array_column(self::getFields(), 'field'), $diffFields);
    }

    public static function getFields() {
        $sql = 'select fields_name as "field",fields_type as "type",fields_not_null as "null",fields_key_name as "key",fields_default as "default",fields_default as "extra" from table_msg(\''.static::$table.'\')';
        $fields = Db::table(static::$table)->query($sql);
        return $fields;
    }

    public static function queryChartData($sql){
        // LogHelper::logDebug('Chart sql: '.$sql, LogHelper::LEVEL_DEBUG);
        $data = Db::query($sql);
        return $data;
    }

    public static function asJsonArray($data, $options = []) {
        if (ArrayHelper::countDimension($data) <= 1) {
            if (empty($data)) {
                return null;
            } else {
                return get_called_class()::asJson($data, $options);
            }
        } else {
            $result = [];
            foreach ($data as $hash) {
                $result[] = get_called_class()::asJson($hash, $options);
            }
            return $result;
        }
    }

    /**
     * @param $where
     * @param string $field
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findByIfEmpty($where,$msg = '', $field = '*'){
        $data = Db::table(static::$table)
            ->field($field)
            ->where($where)
            ->find();
        empty_error($data,static::$table,$msg);
        return $data;
    }
    /**
     * 根据ID查询数据
     * @param int $id 主键ID
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @return array 返回查询数据
     */
    public static function findById($id, $field = '*')
    {
        return Db::table(static::$table)
            ->field($field)
            ->where('id', $id)
            ->find();
    }

    /**
     * @param $where
     * @param string $field
     * @throws \think\exception\DbException
     */
    public static function findInfoByWhere($where, $field = '*')
    {
        return Db::table(static::$table)
            ->field($field)
            ->where($where)
            ->find();
    }

    /**
     * @return mixed
     */
    public static function uuid()
    {
        $uuid = Db::query('select uuid_generate_v4()');
        return $uuid['0']['uuid_generate_v4'];
    }

    /**
     * @param array $data
     * @param bool $uuidAsKey
     * @return string
     */
    public static function insert($data, $uuidAsKey = true): string
    {
        $data['created_at'] = StringHepler::time();
        if (static::$table != 'versions') $data['updated_at'] = StringHepler::time();
        $result = StringHepler::buildInsertData($data);
        $key = $result['key'];
        $val = $result['val'];
        if ($uuidAsKey) {
            $key = 'id,' . $key;
            $id =  StringHepler::uuid();
            $val = '\'' . $id . '\',' . $val;
        } else {
             return self::insertById($data);
        }

        $sql = 'INSERT INTO "public".' . static::$table . ' (' . $key . ') VALUES (' . $val . ')';
        // echo $sql;
        Db::query($sql);
        if (!$uuidAsKey) {
            $id = Db::query('select last_value AS curr_id from '.static::$table.'_id_seq;')[0]['curr_id'];
        }
        return $id;
    }


    /**
     * 后台修改并写入管理员操作日志
     * @param array $data
     * @param $where
     * @param $userInfo
     * @return int|string
     * @throws Exception
     */
    public static function updateAndLog(array $data, $where, $byAdminUser = true)
    {
        $user_id = 0;
        if ($byAdminUser) {
            $user_id = (new BaseAdminController())->getUserInfo()['user_id'];
        }
        $beforeAttrs = self::findInfoByWhere($where);
        $bool = self::update($data, $where);

        $afterAttrs = array_diff_assoc($data, $beforeAttrs);
        self::logAdmin($user_id, $beforeAttrs['id'], 'update', $afterAttrs);
        return $bool;
    }

    /**
     * 后台insert并写入管理员操作日志
     * @param array $data
     * @param bool $uuid
     * @return int|string
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public static function insertAndLog(array $data, $uuidAsKey = true){
        $id = self::insert($data, $uuidAsKey);
        $user_id = (new BaseAdminController())->getUserInfo()['user_id'];
        self::logAdmin($user_id, $id, 'create', $data);
        return $id;
    }

    /**
     * @param $where
     * @return int
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public static function deleteAndLog($where)
    {
        $data = self::find($where);
        $bool = self::delete($where);
        if ($bool) {
            $user_id = (new BaseAdminController())->getUserInfo()['user_id'];
            self::logAdmin($user_id, $data['id'], 'delete', $data);
        }
        return $bool;
    }

    /**
     * @param $where
     * @return int
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public static function delete($where)
    {
        return Db::table(static::$table)
            ->where($where)
            ->delete();
    }

    /**
     * @param $userId
     * @param $itemId
     * @param $action
     * @param array $updatedAttrs
     * @return bool
     */
    public static function logAdmin($userId, $itemId, $action, $updatedAttrs = []) {
        if (empty($updatedAttrs)) return false;
        $table = static::$table;
        if (gettype($updatedAttrs) == 'string') {
            $object = $updatedAttrs;
        } else {
            $object = '--- ';
            $index = 0;
            foreach ($updatedAttrs as $key => $value) {
                if($key !='updated_at'){
                    if ($index == 0) {
                        $object .= $key . ': ' . $value;
                    } else {
                        $object .= '  ' . $key . ': ' . $value;
                    }
                    // $object .= $key . ':' . $updatedAttrs[$key] . ' -> ' . $value . ',';
                    $index += 1;
                }
            }
        }

        $version = [
            'client_ip' => DevicesService::getClientIp(),
            'item_type' => StringHepler::camelize(static::$table),
            'item_id' => $itemId,
            'event' => $action,
            'whodunnit' => $userId,
            'object' => $object,
            'created_at' => StringHepler::time(),
        ];
        static::$table = 'versions';
        self::insert($version);
        static::$table = $table;
        return true;
    }

    /**
     * @param array $data 需要更新的数据
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @return int 受影响的数量
     */
    public static function update(array $data, $where)
    {
        $data['updated_at'] = StringHepler::time();
        return Db::table(static::$table)
            ->where($where)
            ->update($data);
    }

    /**
     * @param array $data
     * @param bool $uuidAsKey
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function createBy(array $data, $uuidAsKey = true) {
        $id = self::insert($data, $uuidAsKey);
        return self::findById($id);
    }

    /**
     * @param $where
     * @param array $data
     * @param bool $is_merge
     * @param bool $uuidAsKey
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function findOrCreateBy($where, array $data = [], $is_merge = true, $uuidAsKey = true)
    {
        // LogHelper::logSubs('findOrCreate: '.json_encode($where).', data: '.json_encode($data), LogHelper::LEVEL_DEBUG);
        if (empty($where)) {
            return [];
        }
        $info = self::findInfoByWhere($where);
        if ($is_merge) $data = array_merge($data, $where);
        if (empty($info)) {
            $info = self::createBy($data, $uuidAsKey);
        } else {
            foreach ($data as $key => $val) {
                if (isset($info[$key]) && empty($info[$key]) && $info[$key] !== 0) {
                    if($val != $info[$key]){
                        $arr[$key] = $val;
                    }
                }
            }
            if (isset($arr)) {
                self::update($arr, $where);
                $info = self::findInfoByWhere($where);
            }
        }
        return $info;
    }

    /**
     * 根据ID更新数据
     * @param array $data 需要更新的数据
     * @param int $id 主键ID
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @return int 受影响的数量
     */
    public static function insertById(array $data)
    {
        $data['created_at'] = StringHepler::time();
        $data['updated_at'] = StringHepler::time();
        return Db::table(static::$table)
            ->insertGetId($data);
    }

    /**
     * @param array $list
     * @param bool $valid
     * @return int|string
     * @throws Exception
     */
    public static function batchInsert($key, $value)
    {
        return Db::query("insert into " . static::$table . " (" . $key . ") values " . $value);
    }

    /**
     * 根据id 列表查询数据
     * @param array $ids 主键ID列表
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function selectByIds($ids, $field = '*')
    {
        return Db::table(static::$table)
            ->field($field)
            ->where('id', 'in', $ids)
            ->select();
    }

    /**
     * 分页
     * @param $limit
     * @param $page
     * @return array
     * @throws \think\exception\DbException
     */
    public static function page($limit, $page)
    {
        return Db::table(static::$table)
            ->order('id desc')
            ->paginate($limit, false, ['page' => $page])
            ->toArray();
    }

    /**
     * 统计
     * @param array $condition
     * @return int|string
     */
    public static function count($condition = '')
    {
        return Db::table(static::$table)
            ->where($condition)
            ->count(1);

    }

    /**
     * 查询
     * @throws \think\exception\DbException
     */
    public static function select($condition, $field = '*', $order = 'id desc')
    {
        return Db::table(static::$table)
            ->field($field)
            ->where($condition)
            ->order($order)
            ->select();
    }

    /**
     * @param $id
     * @param $value
     * @return mixed
     */
    public static function value($value, $filter, $order = '')
    {
        return Db::table(static::$table)
            ->where($filter)
            ->order($order)
            ->value($value);
    }


    /**
     * @param array $condition
     * @param $field
     * @param $num
     * @return int|true
     * @throws \think\Exception
     */
    public static function inc(array $condition, $field, $num)
    {
        return Db::table(static::$table)
            ->where($condition)
            ->setInc($field, $num);
    }

    /**
     * @param array $condition
     * @param $field
     * @param $num
     * @return int|true
     * @throws \think\Exception
     */
    public static function dec(array $condition, $field, $num)
    {
        return Db::table(static::$table)
            ->where($condition)
            ->setDec($field, $num);
    }

    /**
     * @param $condition
     * @param string $filter_str
     * @param string $order
     * @throws \think\exception\DbException
     */
    public static function find($condition, $filter_str = '', $order = 'created_at DESC') {
        return Db::table(static::$table)
            ->where($condition)
            ->where($filter_str)
            ->order($order)
            ->find();
    }

    /**
     * @param $condition
     * @param string $filter_str
     * @param string $order
     * @throws \think\exception\DbException
     */
    public static function where($condition, $filter_str = '', $order = 'created_at DESC') {
        return Db::table(static::$table)
            ->where($condition)
            ->where($filter_str)
            ->order($order)
            ->select();
    }

    /**
     * @param $data = ['a6952465-11ab-4f21-8f33-9a7da5ddf3b2' => ['used_times' => 1,'times'=>2],'16d6a641-8bd8-174e-bef2-cb091822f7b7' => ['used_times' => 1,'times'=>2]]
     * @param string $caseColumn
     * @param string $whereColumn
     * @return mixed
     */
    public static function batchUpdate($data, $whereColumn = 'ID')
    {
        $data_keys = array_keys($data);
        //拼接批量更新sql语句
        $sql = "UPDATE " . static::$table . " SET ";
        //合成sql语句
        foreach (current($data) as $key => $value) {
            $sql .= "{$key} = CASE " . $whereColumn . " ";
            foreach ($data as $data_key => $data_value) {
                if (!isset($when)) {
                    if (is_string($data_key)) {
                        $when = '\'%s\'';
                    } else {
                        $when = '%s';
                    }
                    if (is_string($data_value[$key])) {
                        $then = '\'%s\'';
                    } else {
                        $then = '%s';
                    }
                }
                $sql .= sprintf("WHEN " . $when . " THEN " . $then . " ", $data_key, $data_value[$key]);
            }
            $sql .= "END, ";
        }
        //把最后一个,去掉
        $sql = substr($sql, 0, strrpos($sql, ','));

        $sql .= ', updated_at = \'' . StringHepler::time() . '\'::TIMESTAMP';
        if (is_string(current($data_keys))) {
            $ids = '';
            foreach ($data_keys as $key) {
                $ids .= '\'' . $key . '\',';
            }
            $ids = rtrim($ids, ',');
        } elseif (is_int(current($data_keys))) {
            $ids = implode(',', $data_keys);
        }
        //合并所有id
        $sql .= " WHERE " . $whereColumn . " IN ({$ids})";
        return Db::query($sql);
    }

    /**
     * @param $column
     * @param $str
     * @param string $field
     * @throws \think\exception\DbException
     */
    public static function whereInSelect($column, $str, $field = '*')
    {
        return Db::table(static::$table)
            ->field($field)
            ->whereIn($column, $str)
            ->select();
    }

    /**
     * @param $column
     * @param $str
     * @param $data
     * @return int|string
     * @throws Exception
     */
    public static function whereInUpdate($str, $data, $column = 'id')
    {
        return Db::table(static::$table)
            ->whereIn($column, $str)
            ->update($data);
    }

    public static function bacthInsertById($data)
    {
        return Db::table(static::$table)
            ->insertAll($data);
    }
}