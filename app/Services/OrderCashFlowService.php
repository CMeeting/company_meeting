<?php


namespace App\Services;


use App\Models\OrderCashFlow;

class OrderCashFlowService
{
    public function getPageByOrderId($order_id){
        return OrderCashFlow::getPageByOrderId($order_id);
    }
}