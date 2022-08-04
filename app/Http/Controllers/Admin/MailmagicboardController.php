<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogTagRequest;
use Illuminate\Http\Request;
use App\Services\MailmagicboardService;

class MailmagicboardController extends BaseController {

    /*
     * 邮件模板列表
     * */
    public function mailmagic_list(Request $request)
    {
        $param = $request->input();
        $maile = new MailmagicboardService();
        $data = $maile->data_list($param);
        return $this->view('mailmagiclist',['data'=>$data]);
    }


}
