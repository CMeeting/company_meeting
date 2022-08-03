<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;
use App\Services\ApidocumentationService;
use App\Services\SdkclassificationService;
use Illuminate\Http\Request;

class DocumentationController
{

    public function initialize()
    {

    }
     public function sdkIndex(Request $request){
        $Apidocumentationservice= new ApidocumentationService();
         $param = $request->all();
         if(!$param || !isset($param['platformname'])){
             return json_encode(['data'=>'','code'=>403,'msg'=>"缺少参数"]);
         }
         $data=$Apidocumentationservice->getdata($param);
         return json_encode(['data'=>$data,'code'=>200,'msg'=>"success"]);
     }

    public function sdkInfo(Request $request){
        $Apidocumentationservice= new ApidocumentationService();
        $param = $request->all();
        if(!$param || !isset($param['slugs']) || !isset($param['platformname']) || !isset($param['category'])){
            return json_encode(['data'=>'','code'=>403,'msg'=>"缺少参数"]);
        }
        $data=$Apidocumentationservice->getInfo($param);
        return json_encode(['data'=>$data,'code'=>200,'msg'=>"success"]);
    }
}
