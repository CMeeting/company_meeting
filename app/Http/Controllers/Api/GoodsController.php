<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2020/1/8
 * Time: 14:20
 */

namespace App\Http\Controllers\Api;

use App\Services\GoodsService;
use Illuminate\Http\Request;

class GoodsController
{

    public function getGoods(Request $request){
        $GoodsService= new GoodsService();
        $data=$GoodsService->get_data();
        return \Response::json(['data'=>$data,'code'=>200,'msg'=>"success"]);
    }

}