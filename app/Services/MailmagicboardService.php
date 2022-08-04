<?php

namespace App\Services;

use App\Models\Mailmagicboard as mail;

class MailmagicboardService
{

    public function __construct()
    {

    }

    public function data_list(){

            $where='deleted = 0';

            $email=new mail();
            $data=$email->select();
            return $data;

    }



}