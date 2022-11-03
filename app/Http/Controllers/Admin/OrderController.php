<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\OrdersService;
use App\Services\GoodsService;

class OrderController extends BaseController {


    public function index()
    {
        $param = request()->input();
        $GoodsService = new OrdersService();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["type"] = isset($param['type']) ? $param['type'] : "";
        $query["details_type"] = isset($param['details_type']) ? $param['details_type'] : "";
        $query["status"] = isset($param['status']) ? $param['status'] : "";
        $query["pay_at"] = isset($param['pay_at']) ? $param['pay_at'] : "";
        $query["endpay_at"] = isset($param['endpay_at']) ? $param['endpay_at'] : "";
        $query["shelf_at"] = isset($param['shelf_at']) ? $param['shelf_at'] : "";
        $query["endshelf_at"] = isset($param['endshelf_at']) ? $param['endshelf_at'] : "";
        $query['export'] = array_get($param, 'export', 0);
        $query ['field'] = array_get($param, 'field', '');

        $data = $GoodsService->data_list($query);
//        if($query['export'] == 1){
//            return $GoodsService->export($data, $query['field']);
//        }

        return $this->view('index',['data'=>$data,'query'=>$query]);
    }

    public function create(){
        $GoodsService = new GoodsService();
        $categorical_data = $GoodsService->threelevellinkage();
        return $this->view('create',['lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'lv3'=>json_encode($categorical_data['arr3'])]);
    }

    public function createrun(Request $request){
        $param = $request->input();
        $GoodsService = new OrdersService();
        $rest=$GoodsService->rundata($param);
        return $rest;
    }

}
