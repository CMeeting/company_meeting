<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\OrdersService;

class OrderController extends BaseController {


    public function index()
    {
        $param = request()->input();
        $GoodsService = new OrdersService();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["level1"] = isset($param['level1']) ? $param['level1'] : "";
        $query["level2"] = isset($param['level2']) ? $param['level2'] : "";
        $query["level3"] = isset($param['level3']) ? $param['level3'] : "";
        $query["status"] = isset($param['status']) ? $param['status'] : "";
        $query["start_date"] = isset($param['start_date']) ? $param['start_date'] : "";
        $query["end_date"] = isset($param['end_date']) ? $param['end_date'] : "";
        $query["updated_at"] = isset($param['updated_at']) ? $param['updated_at'] : "";
        $query["endupdated_at"] = isset($param['endupdated_at']) ? $param['endupdated_at'] : "";
        $query["shelf_at"] = isset($param['shelf_at']) ? $param['shelf_at'] : "";
        $query["endshelf_at"] = isset($param['endshelf_at']) ? $param['endshelf_at'] : "";
        $query['export'] = array_get($param, 'export', 0);
        $query ['field'] = array_get($param, 'field', '');

        $data = $GoodsService->data_list($query);
        if($query['export'] == 1){
            return $GoodsService->export($data, $query['field']);
        }

        return $this->view('index',['data'=>$data,'query'=>$query]);
    }

}
