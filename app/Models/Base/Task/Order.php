<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\base;
use Illuminate\Database\Eloquent\Model;
/**
 * Description of Order
 *
 * @author 七彩P1
 */
class Order extends Model {
    public  $table ="order";
    
    public $order_status_arr = [1=>"提现中",2=>"转账中",3=>"提现成功",4=>"提现失败"];
    public $type_remark =[1=>"微信",2=>"支付宝"];
}
