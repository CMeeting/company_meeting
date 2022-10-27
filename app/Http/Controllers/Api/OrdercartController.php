<?php


namespace App\Http\Controllers\Api;

use App\Services\CartService;
use App\Services\UserService;
use Illuminate\Http\Request;


class OrdercartController
{

    public function cart(Request $request)
    {
        $cart = new CartService();
        $current_user = UserService::getCurrentUser($request);
        $user_id = $current_user->id;
        $param = $request->all();
        $param['user_id'] = $user_id;
        $data = $cart->updatedata($param);
        return \Response::json($data);
    }

}