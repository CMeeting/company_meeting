<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\GoodsService;

class GoodsController extends BaseController {

    public function index()
    {
        $param = request()->input();
        $GoodsService = new GoodsService();
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
        $query['field'] = array_get($param, 'field', '');
        $categorical_data = $GoodsService->threelevellinkage();
        $data = $GoodsService->data_list($query);
        if($query['export'] == 1){
            return $GoodsService->export($data, $query['field']);
        }

        return $this->view('index',['data'=>$data,'query'=>$query,'lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'lv3'=>json_encode($categorical_data['arr3'])]);
    }

    public function saasIndex(Request $request)
    {
        $GoodsService = new GoodsService();

        $query['info'] = $request->input('info');
        $query['level1'] = $request->input('level1');
        $query['level2'] = $request->input('level2');
        $query['status'] = $request->input('status');
        $query['created_at'] = $request->input('created_at');
        $query['updated_at'] = $request->input('updated_at');
        $query['shelf_at'] = $request->input('shelf_at');
        $query['export'] = $request->input('export');
        $query['field'] = $request->input('field');

        $categorical_data = $GoodsService->threeLevelLinkAgeSaas();
        $data = $GoodsService->dataListSaaS($query);
        if(isset($query['export']) && $query['export'] == 1){
            return $GoodsService->exportSaaS($data, $query['field']);
        }

        $pricing_url = env('WEB_HOST_SAAS') . '/api/pricing';

        return $this->view('saasindex',['data'=>$data,'query'=>$query,'lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'pricing_url'=>$pricing_url]);
    }

    public function creategoods()
    {
        $GoodsService = new GoodsService();
        $categorical_data = $GoodsService->threelevellinkage();
        return $this->view('creategoods',['lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'lv3'=>json_encode($categorical_data['arr3'])]);
    }
    public function createsaasgoods()
    {
        $GoodsService = new GoodsService();
        $categorical_data = $GoodsService->threeLevelLinkAgeSaas();
        $max_sort = $GoodsService->getMaxSort();
        $sort = $max_sort ? ($max_sort + 1) : 1;
        return $this->view('createsaasgoods',['lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']), 'sort'=>$sort]);
    }

    public function createrungoods(Request $request)
    {
        $param = $request->input();
        $GoodsService = new GoodsService();
        if (!empty($param)) {
            $bool = $GoodsService->addEditcaregorical($param);
            if ($bool && $bool === "repeat") {
                flash('该产品已存在/该平台已存在/该License 类型已存在')->error()->important();
                $result['code'] = 200;
                $result['msg'] = "该产品已存在/该平台已存在/该License 类型已存在";
            } else {
                if ($bool) {
                    flash('添加成功')->success()->important();
                    $result['code'] = 1;
                } else {
                    flash('添加失败')->error()->important();
                    $result['code'] = 1;
                }
            }
            return $result;
        }
    }


    public function createrunsaasgoods(Request $request)
    {
        $param = $request->input();
        $GoodsService = new GoodsService();
        if (!empty($param)) {
            $bool = $GoodsService->addsaasEditcaregorical($param);
            if ($bool && $bool === "repeat") {
                flash('该套餐档位商品已存在')->error()->important();
                $result['code'] = 200;
                $result['msg'] = "该套餐档位商品已存在";
            } elseif($bool && $bool === 'repeat_sort'){
                flash('该序号已存在')->error()->important();
                $result['code'] = 200;
                $result['msg'] = "该序号已存在";
            } else {
                if ($bool) {
                    flash('添加成功')->success()->important();
                    $result['code'] = 1;
                } else {
                    flash('添加失败')->error()->important();
                    $result['code'] = 1;
                }
            }
            return $result;
        }
    }

    public function updategoods($id)
    {
        $GoodsService = new GoodsService();
        $data = $GoodsService->getFindcategorical($id);
        $categorical_data = $GoodsService->threelevellinkage();
        return $this->view('updategoods',['lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'lv3'=>json_encode($categorical_data['arr3']),'data'=>$data]);
    }

    public function updatesaasgoods($id)
    {
        $GoodsService = new GoodsService();
        $data = $GoodsService->getsaasFindcategorical($id);
        $categorical_data = $GoodsService->threeLevelLinkAgeSaas();
        return $this->view('updatesaasgoods',['lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'data'=>$data]);
    }

    public function updaterunsaasgoods(Request $request)
    {
        $param = $request->input();
        $GoodsService = new GoodsService();

        if (!empty($param)) {
            $bool = $GoodsService->addsaasEditcaregorical($param);
            if ($bool === "repeat") {
                flash('该套餐档位商品已存在')->error()->important();
                $result['code'] = 200;
                $result['msg'] = "该套餐档位商品已存在";
            } else if($bool === 'repeat_sort'){
                flash('该序号已存在')->error()->important();
                $result['code'] = 200;
                $result['msg'] = "该序号已存在";
            } else{
                if ($bool==1) {
                    $result['code'] = 1;
                }elseif ($bool==0){
                    $result['code'] = 200;
                    $result['msg'] = "暂无更新";
                } else {
                    $result['code'] = 200;
                    $result['msg'] = "修改失败";
                }
            }
            return $result;
        }
    }

    public function updaterungoods(Request $request)
    {
        $param = $request->input();
        $GoodsService = new GoodsService();

        if (!empty($param)) {
            $bool = $GoodsService->addEditcaregorical($param);
            if ($bool == "repeat") {
                flash('该产品已存在/该平台已存在/该License 类型已存在')->error()->important();
                $result['code'] = 200;
                $result['msg'] = "该产品已存在/该平台已存在/该License 类型已存在";
            } else {
                if ($bool==1) {
                    $result['code'] = 1;
                }elseif ($bool==0){
                    $result['code'] = 200;
                    $result['msg'] = "暂无更新";
                } else {
                    $result['code'] = 200;
                    $result['msg'] = "修改失败";
                }
                return $result;
            }
        }
    }

    public function delgoods(Request $request)
    {
        $param = $request->input();
        $GoodsService = new GoodsService();
        $bool = $GoodsService->addEditcaregorical($param);
        if ($bool) {
            return ['code'=>0];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }


    public function show(Request $request)
    {
        $param = $request->input();
        $GoodsService = new GoodsService();
        $bool = $GoodsService->show($param);
//        $data = $GoodsService->getFindcategorical($param['id']);
        if ($bool['code']) {
            return ['code'=>0,'status'=>$bool['status']];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }


    public function info($id)
    {
        $GoodsService = new GoodsService();
        $data = $GoodsService->getFindcategorical($id);
        return $this->view('info',['data'=>$data]);
    }

    public function saasinfo($id)
    {
        $GoodsService = new GoodsService();
        $data = $GoodsService->getsaasFindcategorical($id);
        return $this->view('saasinfo',['data'=>$data]);
    }


}