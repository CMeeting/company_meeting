<?php


namespace App\Services;


use App\Models\ContactEmail;
use App\Models\ContactEmailAttachment;

class ContactEmailService
{
    /**
     * 新增support邮件内容
     * @param $email
     * @param $first_name
     * @param $last_name
     * @param $subject
     * @param $description
     * @return string
     */
    public function add($email, $first_name, $last_name, $subject, $description){
        //保存邮件信息
        $contact_email = new ContactEmail();
        $contact_email->email = $email;
        $contact_email->first_name = $first_name;
        $contact_email->last_name = $last_name;
        $contact_email->subject = $subject;
        $contact_email->description = $description;
        $contact_email->save();

        return $contact_email->id;
    }

    /**
     * 新增附件
     * @param $contact_email_id
     * @param $url
     */
    public function addAttachments($contact_email_id, $url){
        $contact_email_attachment = new ContactEmailAttachment();
        $contact_email_attachment->url = $url;
        $contact_email_attachment->contact_email_id = $contact_email_id;
        $contact_email_attachment->save();
    }

}