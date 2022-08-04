<?php

namespace App\Services;

use App\Models\Mailmagicboard as mail;

class MailmagicboardService
{

    public function __construct()
    {

    }

    public function data_list($param){
            $where = "deleted=0";
            $email = new mail();
            $data = $email->whereRaw($where)->orderByRaw('id desc')->paginate(10);
            return $data;
    }



}