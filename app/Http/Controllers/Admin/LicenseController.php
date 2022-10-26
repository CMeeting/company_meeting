<?php

namespace App\Http\Controllers\Admin;

use App\Services\LicenseService;
use Illuminate\Http\Request;
use App\Services\GoodsService;

class LicenseController extends BaseController
{

    /**
     * 列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $param = request()->input();
        $license = new LicenseService();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["type"] = isset($param['type']) ? $param['type'] : "";
        $query["level1"] = isset($param['level1']) ? $param['level1'] : "";
        $query["level2"] = isset($param['level2']) ? $param['level2'] : "";
        $query["level3"] = isset($param['level3']) ? $param['level3'] : "";
        $query["created_start"] = isset($param['created_start']) ? $param['created_start'] : "";
        $query["created_end"] = isset($param['created_end']) ? $param['created_end'] : "";
        $query["expire_start"] = isset($param['expire_start']) ? $param['expire_start'] : "";
        $query["expire_end"] = isset($param['expire_end']) ? $param['expire_end'] : "";
        $license_type = config("constants.license_type");
        $data = $license->list($query);
        $products = $license->getGoodsClassifications(['lv' => 1]);
        $platforms = $license->getGoodsClassifications(['lv' => 2]);
        $license_types = $license->getGoodsClassifications(['lv' => 3]);
        return $this->view('index', compact('data', 'query', 'license_type', 'products', 'platforms', 'license_types'));
    }

    /**
     * 改变状态
     * @return array
     */
    public function changeStatus()
    {
        $param = request()->input();
        $license = new LicenseService();
        $license->changeStatus($param);
        return ["code" => 200, "msg" => "操作成功"];
    }

    /**
     * 查看详情
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info($id)
    {
        $license = new LicenseService();
        $info = $license->getInfo($id);
        return $this->view('info', compact('info'));
    }


    public function createLicense()
    {
        $GoodsService = new GoodsService();
        $categorical_data = $GoodsService->threelevellinkage();
        return $this->view('createLicense', ['lv1' => json_encode($categorical_data['arr1']), 'lv2' => json_encode($categorical_data['arr2']), 'lv3' => json_encode($categorical_data['arr3'])]);
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

}