<?php


namespace App\Services;


use Illuminate\Support\Facades\Mail;

class MailHelperService
{
    public static function setAccount($account){
        $transport = new \Swift_SmtpTransport(
            config("my_emails.$account.smtp"),
            config("my_emails.$account.port"),
            config("my_emails.$account.encryption")
        );

        $transport->setUsername(config("my_emails.$account.email"));
        $transport->setPassword(config("my_emails.$account.password"));

        $mailer = new \Swift_Mailer($transport);

        Mail::setSwiftMailer($mailer);
        Mail::alwaysFrom(config("my_emails.$account.email"), config("my_emails.$account.name"));
    }
}