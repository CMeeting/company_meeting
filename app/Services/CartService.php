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

use App\Models\CartModels as cart;
use App\Models\Goods;
use App\Models\Goodsclassification;
use Auth;

class CartService
{
    public function __construct()
    {

    }

    public function updatedata($data)
    {
        $cart = new cart();
        $goods = new goods();
        if (isset($data['id'])) {
            $arr = ['pay_years' => $data['pay_years']];
            $rest = $cart->_update($arr, "id='{$data['id']}'");
        }elseif (isset($data['delid'])){
            $rest = $cart->_delete([["id","=",$data['delid']]]);
        } else {
            if(!isset($data['goods_id'])){
                $goods_data = $goods->_find("level1='{$data['products_id']}' and level2='{$data['platform_id']}' and level3='{$data['licensetype_id']}' and deleted=0 and status=1");
            }else{
                $goods_data = $goods->_find("id='{$data['goods_id']}' and deleted=0 and status=1");
            }
            $goods_data = $goods->objToArr($goods_data);
            if (!$goods_data) {
                return ['code' => 403, 'msg' => "该商品不存在或已下架"];
            }
            $cart_info = $cart->_find("goods_id='{$goods_data['id']}' and user_id='{$data['user_id']}' and appid='{$data['appid']}'");
            $cart_info = $cart->objToArr($cart_info);
            if ($cart_info) {
                $arr = ['pay_years' => $data['pay_years'] + $cart_info['pay_years']];
                $rest = $cart->_update($arr, "id='{$cart_info['id']}'");
            } else {
                $arr = [
                    'user_id' => $data['user_id'],
                    'goods_id' => $goods_data['id'],
                    'level1' => $data['products_id'],
                    'level2' => $data['platform_id'],
                    'level3' => $data['licensetype_id'],
                    'appid' => $data['appid'],
                    'pay_years' => $data['pay_years']
                ];
                $rest = $cart->insertGetId($arr);
            }
        }
       if($rest){
           $list=$cart->_where("user_id='{$data['user_id']}'");
           if($list){
               $goodsinfo=$this->get_goods();
               foreach ($list as $k=>$v){
                   $list[$k]['price']=$goodsinfo['goods'][$v['goods_id']]['price']*$v['pay_years'];
                   $list[$k]['goodsname']=$goodsinfo['fenlei'][$v['level1']]['title'].$goodsinfo['fenlei'][$v['level2']]['title'].$goodsinfo['fenlei'][$v['level3']]['title'];
               }
           }
           return ['code'=>200,'msg'=>"ok",'data'=>$list];
       }else{
           return ['code'=>403,'msg'=>"未知错误"];
       }

    }


    public function getdata($user_id){
        $cart = new cart();
        $list=$cart->_where("user_id='{$user_id}'");
        if($list){
            $goodsinfo=$this->get_goods();
            foreach ($list as $k=>$v){
                $list[$k]['price']=$goodsinfo['goods'][$v['goods_id']]['price']*$v['pay_years'];
                $list[$k]['goodsname']=$goodsinfo['fenlei'][$v['level1']]['title'].$goodsinfo['fenlei'][$v['level2']]['title'].$goodsinfo['fenlei'][$v['level3']]['title'];
            }
        }
        return ['code'=>200,'msg'=>"ok",'data'=>$list];
    }

    public function get_goods(){
        $goodsfenlei = new Goodsclassification();
        $goods = new Goods();
        $fenleidata = $goodsfenlei->_where("deleted=0");
        $goodsdata = $goods->_where("status=1 and deleted=0");
        $arr=array();
        foreach ($fenleidata as $k=>$v){
            $arr['fenlei'][$v['id']]=$v;
        }
        foreach ($goodsdata as $k=>$v){
            $arr['goods'][$v['id']]=$v;
        }
        return $arr;
    }


}