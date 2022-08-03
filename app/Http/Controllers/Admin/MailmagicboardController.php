<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogTagRequest;
use Illuminate\Http\Request;
use App\Services\AdminsService;

class MailmagicboardController extends BaseController {


    /**
     * 平台/版本主页
     * @return mixed
     */
    public function platformVersion()
    {
        $documentation = new DocumentationService();
        $data = $documentation->getCategoricalData();
        return $this->view('platformversion',['cateList'=>$data['cateList'],'childCateList'=>$data['childCateList']]);
    }


}
