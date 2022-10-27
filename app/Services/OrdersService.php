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

use App\Export\GoodsExport;
use App\Export\UserExport;
use App\Models\Order;
use App\Models\OrderGoods;
use Auth;

class OrdersService
{
    public function __construct()
    {

    }


    public function data_list($param)
    {
        $where = "deleted=0";
        if ($param['info']) {
            $where .= " and {$param['query_type']}={$param['info']}";
        }
        if ($param['level1']) {
            $where .= " and level1={$param['level1']}";
        }
        if ($param['level2']) {
            $where .= " and level2={$param['level2']}";
        }
        if ($param['level3']) {
            $where .= " and level3={$param['level3']}";
        }
        if ($param['status']) {
            $param['status'] = $param['status'] - 1;
            $where .= " and status={$param['status']}";
        }
        if (isset($param['start_date']) && $param['start_date'] && isset($param['end_date']) && $param['end_date']) {
            $where .= " AND created_at BETWEEN '" . $param['start_date'] . "' AND '" . $param['end_date'] . "'";
        } elseif (isset($param['start_date']) && $param['start_date'] && empty($param['end_date'])) {
            $where .= " AND created_at >= '" . $param['start_date'] . "'";
        } elseif (isset($param['end_date']) && $param['end_date'] && empty($param['start_date'])) {
            $where .= " AND created_at <= '" . $param['end_date'] . "'";
        }

        if (isset($param['updated_at']) && $param['updated_at'] && isset($param['endupdated_at']) && $param['endupdated_at']) {
            $where .= " AND updated_at BETWEEN '" . $param['updated_at'] . "' AND '" . $param['endupdated_at'] . "'";
        } elseif (isset($param['updated_at']) && $param['updated_at'] && empty($param['endupdated_at'])) {
            $where .= " AND updated_at >= '" . $param['updated_at'] . "'";
        } elseif (isset($param['endupdated_at']) && $param['endupdated_at'] && empty($param['updated_at'])) {
            $where .= " AND updated_at <= '" . $param['endupdated_at'] . "'";
        }

        if (isset($param['shelf_at']) && $param['shelf_at'] && isset($param['endshelf_at']) && $param['endshelf_at']) {
            $where .= " AND shelf_at BETWEEN '" . $param['shelf_at'] . "' AND '" . $param['endshelf_at'] . "'";
        } elseif (isset($param['shelf_at']) && $param['shelf_at'] && empty($param['endshelf_at'])) {
            $where .= " AND shelf_at >= '" . $param['shelf_at'] . "'";
        } elseif (isset($param['endshelf_at']) && $param['endshelf_at'] && empty($param['shelf_at'])) {
            $where .= " AND shelf_at <= '" . $param['endshelf_at'] . "'";
        }

        $goods = new Order();

        if ($param['export'] == 1) {
            return $goods->whereRaw($where)->orderByRaw('id desc')->get()->toArray();
        } else {
            $data = $goods->whereRaw($where)->orderByRaw('id desc')->paginate(10);
        }

        if (!empty($data)) {
            $classification = $this->assembly_classification();
            foreach ($data as $k => $v) {
                $v->products = $classification[$v['level1']]['title'];
                $v->platform = $classification[$v['level2']]['title'];
                $v->licensie = $classification[$v['level3']]['title'];
            }
        }
        return $data;
    }
}