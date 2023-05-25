<?php


namespace App\Services;


use App\Models\User;
use App\Models\UserBillingInformation;
use App\Models\WebViewerLicense;
use App\Models\WebViewerLicenseDomain;
use Carbon\Carbon;

class WebViewerLicenseService
{
    /**
     * webviewer生成license
     * @param $email
     * @param $full_name
     * @param $company
     * @param $type
     * @param $expiration
     * @param $domain_info
     * @return array
     */
   public function generate($email, $full_name, $company, $type, $expiration, $domain_info){
       try{
           \DB::beginTransaction();

           $userService = new UserService();
           $exists_user = User::where('email', $email)->first();
           if($exists_user instanceof User){
               $user_id = $exists_user->id;
           }else{
               //生成密码 规则：邮箱拼接加盐码  方便后续发送给用户 目前密码用的MD5处理 无法反向解析
               $encryption = new EncryptionService();
               $slat_code = $encryption->getSaltCode($email);
               $password = $email . $slat_code;
               $user_id = $userService->add($email, $full_name, $password, User::SOURCE_3_WEBVIEWER, User::IS_VERIFY_2_YES);

               //保存用户公司信息
               $bill = new UserBillingInformation();
               $bill->user_id = $user_id;
               $bill->company = $company;
               $bill->save();
           }

           //新增webviewer license
           $license = new WebViewerLicense();
           $license_code = $license->generateCode();
           $license_id = $license->add($user_id, $type, $license_code, $expiration);

           //新增域名
           $license_domain = new WebViewerLicenseDomain();
           foreach ($domain_info as $domain => $server_region){
               $license_domain->add($license_id, $domain, $server_region);
           }
           \DB::commit();
       }catch (\Exception $e){
           \DB::rollBack();
           \Log::error('webviewer生成license错误', ['email'=>$email, 'message'=>$e->getMessage()]);
           return ['code'=>500, 'message'=>'系统错误', 'data'=>[]];
       }

       return ['code'=>200, 'message'=>'success', 'data'=>$license_code];
   }

    /**
     * 序列码验证
     * @param $license_code
     * @param $url
     * @return array
     */
   public function verify($license_code, $url){
       $license_model = WebViewerLicense::where('license_code', $license_code)->first();

       if(!$license_model instanceof WebViewerLicense){
           return ['code'=>500, 'message'=>'license code not exists', 'data'=>[]];
       }

       //验证 请求的域名是否已license的域名结尾
       $allow_domain = WebViewerLicenseDomain::where('license_id', $license_model->id)->pluck('domain')->toArray();
       $host = parse_url($url, PHP_URL_HOST);
       $pass_domain = '';
       foreach ($allow_domain as $domain){
           $len = strlen($domain);
           if(substr($host, -$len) === $domain){
               $pass_domain = $domain;
               break;
           }
       }
       if(!$pass_domain){
           return ['code'=>500, 'message'=>'invalid domain', 'data'=>[]];
       }

       //验证时间
       $end_date = Carbon::parse($license_model->expiration);
       if($end_date->lt(Carbon::now())){
           return ['code'=>500, 'message'=>'license expired', 'data'=>[]];
       }

       //验证通过生成token
       //有效期为一天,如果大于License有效期则为 license的有效期
       $expire_date = Carbon::now()->addDay();
       if($expire_date->gt($end_date)){
           $expire_date = $end_date;
       }

       $data = [
           'license_id' => $license_model->id,
           'user_id' => $license_model->user_id,
           'domain' => $allow_domain,
           'expire_date' => $expire_date->format('Y-m-d H:i:s')
       ];

       $encryptionService = new EncryptionService();
       $token = $encryptionService->encryption(json_encode($data));

       $data = json_encode(['token'=>$token, 'domain'=>$pass_domain]);
       $data = openssl_encrypt($data, 'AES-128-CBC', $encryptionService->key, 0, $encryptionService->iv);

       return ['code'=>200, 'message'=>'success', 'data'=>$data];
   }

   public function getTopDomain($domain){
       $host = parse_url($domain, PHP_URL_HOST);
       $data = explode('.', $host);
       $n = count($data);

       //判断是否是双后缀
       $preg = '/[\w].+\.(com|net|org|gov|edu)\.cn$/';
       if(($n > 2) &&  preg_match($preg, $host)){
           //双后缀取后3位
           $host = $data[$n-3].'.'.$data[$n-2].'.'.$data[$n-1];
       }else{
           //非双后缀取后两位
           $host = $data[$n-2].'.'.$data[$n-1];
       }
       return $host;
   }
}