<?php

namespace App\Http\Controllers\Admin;

use App\Models\Goodsclassification;
use App\Services\GoodsclassificationService;
use Illuminate\Http\Request;
use App\Services\OrdersService;
use App\Services\GoodsService;
use PDF;
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
        $query["pay_type"] = isset($param['pay_type']) ? $param['pay_type'] : "";
        $query['export'] = array_get($param, 'export', 0);
        $query ['field'] = array_get($param, 'field', '');
        $data = $GoodsService->data_list($query);
        $sum = $GoodsService->sum_data($query);

        if($query['export'] == 1){
            return $GoodsService->export($data, $query['field']);
        }
        return $this->view('index',['data'=>$data,'query'=>$query,'sum'=>$sum]);
    }
    public function saasindex()
    {
        $param = request()->input();
        $GoodsService = new OrdersService();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["type"] = isset($param['type']) ? $param['type'] : "";
        $query["status"] = isset($param['status']) ? $param['status'] : "";
//        $query["pay_at"] = isset($param['pay_at']) ? $param['pay_at'] : "";
//        $query["endpay_at"] = isset($param['endpay_at']) ? $param['endpay_at'] : "";
        $query["created_at"] = isset($param['created_at']) ? $param['created_at'] : "";
        $query["pay_type"] = isset($param['pay_type']) ? $param['pay_type'] : "";
        $query['combo'] = array_get($param, 'combo', '');
        $query['gear'] = array_get($param, 'gear', '');
        $query['export'] = array_get($param, 'export', 0);
        $query ['field'] = array_get($param, 'field', '');
        $data = $GoodsService->data_saaslist($query);
        $sum = $GoodsService->sum_saasdata($query);
        if($query['export'] == 1){
            return $GoodsService->exportSaaS($data, $query['field']);
        }

        //获取套餐和档位
        $combos = Goodsclassification::getComboOrGear(1);
        $gears = Goodsclassification::getGearGroupByCombo();

        return $this->view('saasindex',['data'=>$data,'query'=>$query,'sum'=>$sum, 'combos'=>$combos, 'gears'=>$gears]);
    }

    public function create(){
        $GoodsService = new GoodsService();
        $categorical_data = $GoodsService->threelevellinkage();
        return $this->view('create',['lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'lv3'=>json_encode($categorical_data['arr3'])]);
    }
    public function saascreate(){
        $GoodsService = new GoodsService();
        $categorical_data = $GoodsService->threeLevelLinkAgeSaas();
        return $this->view('saascreate',['lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2'])]);
    }

    public function createrun(Request $request){
        $param = $request->input();
        $GoodsService = new OrdersService();
        $rest=$GoodsService->rundata($param);
        return $rest;
    }

    public function saascreaterun(Request $request){
        $param = $request->input();
        $GoodsService = new OrdersService();
        $rest=$GoodsService->saasrundata($param);
        return $rest;
    }


    /**
     * 订单详情
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getinfo($id)
    {
        $order = new OrdersService();
        $data = $order->data_info($id);
        $info = $order->getOrderInfo($id);//获取发票信息
        return $this->view('info', ['data' => $data, 'info' => $info]);
    }


    public function getsaasinfo($id){
        $order = new OrdersService();
        $data = $order->data_saasinfo($id);
//        $info = $order->getOrderInfo($id);//获取发票信息
        return $this->view('saasinfo', ['data' => $data]);
    }

    public function updatestatus(Request $request){
        $param = $request->input();
        $GoodsService = new OrdersService();
        $rest=$GoodsService->update_status($param['id']);
        return $rest;
    }







}
