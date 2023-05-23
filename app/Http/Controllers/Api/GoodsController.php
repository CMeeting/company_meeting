<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;

use App\Models\Goods;
use App\Models\Goodsclassification;
use App\Services\GoodsService;
use Illuminate\Http\Request;

class GoodsController
{

    public function getGoods(Request $request){
        $GoodsService= new GoodsService();
        $data=$GoodsService->get_data();
        return \Response::json(['data'=>$data,'code'=>200,'msg'=>"success"]);
    }

    /**
     * 获取SaaS产品
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSaaSGoods(){
        $service = new GoodsService();
        $data = $service->getSaaSGoods();

        return \Response::json(['data'=>$data,'code'=>200,'msg'=>"success"]);
    }

}