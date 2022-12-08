<?php


namespace App\Services;


use App\Models\EmailBlacklist;

class EmailBlacklistService
{
    public function exitsEmail($email){
        return EmailBlacklist::where('email', $email)->exists();
    }

    public function add($email){
        $admin = \Auth::guard('admin')->user();
        $model = new EmailBlacklist();
        $model->email = $email;
        $model->admin_id = $admin->id;
        $model->save();
    }

    public function del($email){
        EmailBlacklist::where('email', $email)->delete();
    }
}