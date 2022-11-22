<?php

namespace App\Http\Controllers\Admin;

use App\Services\GenerateLicenseCodeService;
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
        $GoodsService = new GoodsService();
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
        $query['export'] = array_get($param, 'export', 0);
        $license_type = config("constants.license_type");
        $data = $license->list($query);
        $categorical_data = $GoodsService->threelevellinkage();
        if($query['export'] == 1){
            return $license->export($data, $param['field']);
        }
//        return $this->view('index', compact('data', 'query', 'license_type', 'products', 'platforms', 'license_types'));
        return $this->view('index',['data'=>$data,'license_type'=>$license_type,'query'=>$query,'lv1'=>json_encode($categorical_data['arr1']),'lv2'=>json_encode($categorical_data['arr2']),'lv3'=>json_encode($categorical_data['arr3'])]);
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


    public function createrunLicense(Request $request)
    {
        $param = $request->input();
        $license = new LicenseService();
        $ret=$license->createlicense($param['data']);
        if($ret){
            return ['code'=>1,'msg'=>"OK"];
        }
    }

    public function generateLicenseCode(){
        $generate = new GenerateLicenseCodeService();
        return $generate->generate('ComPDFKit PDF SDK', 'Enterprise License', 'iOS', '1669083426', '1671675425', ['312312'], '123@gmail.com');
    }

}