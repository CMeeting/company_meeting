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

use App\Export\GoodsExport;
use App\Export\UserExport;
use App\Models\Order;
use App\Models\Goods;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\OrderGoods;
use Auth;

class OrdersService
{
    public function __construct()
    {

    }


    public function data_list($param)
    {
        $where = "1=1";
        if ($param['info']) {
            $where .= " and {$param['query_type']}={$param['info']}";
        }
        if ($param['status']) {
            $param['status'] = $param['status'] - 1;
            $where .= " and status={$param['status']}";
        }
        if (isset($param['start_date']) && $param['start_date'] && isset($param['end_date']) && $param['end_date']) {
            $where .= " AND created_at BETWEEN '" . $param['start_date'] . "' AND '" . $param['end_date'] . "'";
        } elseif (isset($param['start_date']) && $param['start_date'] && empty($param['end_date'])) {
            $where .= " AND created_at >= '" . $param['start_date'] . "'";
        } elseif (isset($param['end_date']) && $param['end_date'] && empty($param['start_date'])) {
            $where .= " AND created_at <= '" . $param['end_date'] . "'";
        }

        if (isset($param['updated_at']) && $param['updated_at'] && isset($param['endupdated_at']) && $param['endupdated_at']) {
            $where .= " AND updated_at BETWEEN '" . $param['updated_at'] . "' AND '" . $param['endupdated_at'] . "'";
        } elseif (isset($param['updated_at']) && $param['updated_at'] && empty($param['endupdated_at'])) {
            $where .= " AND updated_at >= '" . $param['updated_at'] . "'";
        } elseif (isset($param['endupdated_at']) && $param['endupdated_at'] && empty($param['updated_at'])) {
            $where .= " AND updated_at <= '" . $param['endupdated_at'] . "'";
        }

        if (isset($param['shelf_at']) && $param['shelf_at'] && isset($param['endshelf_at']) && $param['endshelf_at']) {
            $where .= " AND shelf_at BETWEEN '" . $param['shelf_at'] . "' AND '" . $param['endshelf_at'] . "'";
        } elseif (isset($param['shelf_at']) && $param['shelf_at'] && empty($param['endshelf_at'])) {
            $where .= " AND shelf_at >= '" . $param['shelf_at'] . "'";
        } elseif (isset($param['endshelf_at']) && $param['endshelf_at'] && empty($param['shelf_at'])) {
            $where .= " AND shelf_at <= '" . $param['endshelf_at'] . "'";
        }

        $goods = new Order();

        if ($param['export'] == 1) {
            return $goods->whereRaw($where)->orderByRaw('id desc')->get()->toArray();
        } else {
            $data = $goods->leftJoin('users', 'orders.user_id', '=', 'users.id')->whereRaw($where)->orderByRaw('orders.id desc')->selectRaw("orders.*,users.email")->paginate(10);
        }

//        if (!empty($data)) {
//            $classification = $this->assembly_classification();
//            foreach ($data as $k => $v) {
//                $v->products = $classification[$v['level1']]['title'];
//                $v->platform = $classification[$v['level2']]['title'];
//                $v->licensie = $classification[$v['level3']]['title'];
//            }
//        }
        return $data;
    }

    public function rundata($param){
        $data=$param['data'];
        $user = new User();
        $goods = new Goods();
        $order = new Order();
        $orderGoods = new OrderGoods();
        $is_user=$user->existsEmail($data['email']);
        if(!$is_user){
            $arr['full_name']=$data['email'];
            $arr['email']=$data['email'];
            $arr['flag']=2;
            $arr['created_at']=date("Y-m-d H:i:s");
            $arr['updated_at']=date("Y-m-d H:i:s");
            $user_id= Db::table("users")->insertGetId($arr);
        }else{
            $users=DB::table('users')->where('email', $data['email'])->first();
            $user_id= $users->id;
        }
        $goods_data=$goods->_where("deleted=0 and status=1");
        $arr=[];
        $sumprice=0;
        $goodstotal=0;
        foreach ($data['level1'] as $k=>$v){
            foreach ($goods_data as $ks=>$vs){
                if($v==$vs['level1'] && $data['level2'][$k]==$vs['level2'] && $data['level3'][$k]==$vs['level3']){
                    $goodsid=$vs['id'];
                    $price=$vs['price'];
                }
            }
            $arr[]=[
                'pay_type'=>0,
                'status'=>$data['status'],
                'type'=>1,
                'details_type'=>1,
                'price'=>$price,
                'user_id'=>$user_id,
                'pay_years'=>1,
                'goods_id'=>$goodsid,
                'created_at'=>date("Y-m-d H:i:s"),
                'updated_at'=>date("Y-m-d H:i:s")
            ];
            $goodstotal++;
            $sumprice+=$price;
        }
        $orderno=time();
        $orderdata=[
            'order_no'=>$orderno,
            'pay_type'=>0,
            'status'=>$data['status'],
            'type'=>1,
            'details_type'=>1,
            'price'=>$sumprice,
            'user_id'=>$user_id,
            'goodstotal'=>$goodstotal
        ];

        try {
            $order_id=$order->insertGetId($orderdata);
            foreach ($arr as $k=>$v){
                $arr[$k]['order_id']=$order_id;
                $arr[$k]['order_no']=$orderno;
            }
            $orderGoods->_insert($arr);
            $user_info=$user->_find("id='{$user_id}'");
            $user_info=$user->objToArr($user_info);
            $userprice=$user_info['order_amount']+$sumprice;
            $userorder=$user_info['order_num']+1;
            $user->_update(['order_amount'=>$userprice,'order_num'=>$userorder],"id='{$user_id}'");
        }catch (Exception $e){
            return ['code'=>500, 'message'=>'Invalid Token'];
        }
        return ['code'=>200];
    }
}