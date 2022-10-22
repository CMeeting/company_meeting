<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\LogoutUser;
use App\Models\User;
use App\Services\EmailService;
use App\Services\JWTService;
use App\Services\UserBillingInfoService;
use App\Services\UserService;
use Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;

class UserController extends Controller
{
    /**
     * 注册
     * @param Request $request
     * @return array|JsonResponse
     */
    public function register(Request $request)
    {
        $full_name = trim($request->input('full_name'));
        $email = trim($request->input('email'));
        $password = str_replace(' ', '', $request->input('password'));

        $userService = new UserService();

        $result_full_name = $userService->validateFullName($full_name, 'en');
        if ($result_full_name['code'] != 200) {
            return Response::json(['code'=>500, 'message'=>$result_full_name['msg']]);
        }

        $result_email = $userService->validateEmail($email, 'en');
        if ($result_email['code'] != 200) {
            return Response::json(['code'=>500, 'message'=>$result_email['msg']]);
        }

        $result_password = $userService->validatePassword($password, 'en');
        if ($result_password['code'] != 200) {
            return $result_password;
        }

        $user_id = $userService->add($email, $full_name, $password);

        //TODO 发送注册成功邮件
        $emailService = new EmailService();
        $data['title'] = '注册成功';
        $data['info'] = '感谢注册，账号' . $email;
        $emailService->sendDiyContactEmail($data,1, $email);

        //['email'=>'test@gmail.com', 'iat'=>'签发时间', 'jti'=>'token唯一标识']
        $jti = JWTService::getJTI();
        Cache::add($jti, 1, 60*24);

        $payload = ['email' => $email, 'iat' => time(), 'jti'=>$jti, 'id'=>$user_id];
        $token = JWTService::getToken($payload);

        return Response::json(['code'=>200, 'message'=>'success', 'data'=>['token'=>$token]]);
    }

    /**
     * 登录
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');

        $user = UserService::getByEmail($email);
        if(!$user instanceof User){
            return Response::json(['code'=>500, 'message'=>'Incorrect account or password.']);
        }

        if($user->password != User::encryptPassword($password)){
            return Response::json(['code'=>500, 'message'=>'Incorrect account or password.']);
        }

        $jti = JWTService::getJTI();

        //缓存token
        JWTService::saveToken($email, $jti);

        $payload = ['email' => $email, 'iat' => time(), 'jti'=>$jti];
        $token = JWTService::getToken($payload);

        //记录登录次数
        $userService = new UserService();
        $userService->addLoginTime($user->id);

        return Response::json(['code'=>200, 'message'=>'success', 'data'=>['token'=>$token]]);
    }

    /**
     * 获取用户基本信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserInfo(Request $request){
        $current_user = UserService::getCurrentUser($request);

        return Response::json(['code'=>200, 'message'=>'success', 'data'=>['full_name'=>$current_user->full_name, 'email'=>$current_user->email]]);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request){
        $userService = new UserService();
        $current_user = UserService::getCurrentUser($request);

        $old_password = str_replace(' ', '', $request->input('old_password'));
        $new_password = str_replace(' ', '', $request->input('new_password'));
        $password_confirm = str_replace(' ', '', $request->input('password_confirm'));

        if($old_password == $new_password){
            return Response::json(['code'=>500, 'message'=>'Cannot Match Old Password.']);
        }

        if($current_user->password != User::encryptPassword($old_password)){
            return Response::json(['code'=>500, 'message'=>'Incorrect Old Password.']);
        }

        $password_result = $userService->validatePassword($new_password, 'en');
        if($password_result['code'] != 200){
            return Response::json(['code'=>500, 'message'=>$password_result['msg']]);
        }

        if($new_password != $password_confirm){
            return Response::json(['code'=>500, 'message'=>'The Password and Confirm Password do not match.']);
        }

        $userService->changePassword($current_user, $password_confirm);

        //删除用户token
        JWTService::forgetToken($current_user->email);

        return Response::json(['code'=>200, 'message'=>'success']);
    }

    /**
     * 修改邮箱
     * @param Request $request
     * @return JsonResponse
     */
    public function changeEmail(Request $request){
        $current_user = UserService::getCurrentUser($request);

        $old_email = $current_user->email;

        $password = $request->input('password');
        $email = trim($request->input('email'));

        if($old_email == $email){
            return Response::json(['code'=>500, 'message'=>'Cannot Match Old Email.']);
        }

        if($current_user->password != User::encryptPassword($password)){
            return Response::json(['code'=>500, 'message'=>'Incorrect Old Password.']);
        }

        $userService = new UserService();
        $email_result = $userService->validateEmail($email, 'en', $current_user->id);
        if($email_result['code'] != 200){
            return Response::json(['code'=>500, 'message'=>$email_result['msg']]);
        }

        $userService->changeEmail($current_user, $email);

        //删除token
        JWTService::forgetToken($old_email);

        //TODO 发送邮件
//        $emailService = new EmailService();
//        $data['title'] = '注册成功';
//        $data['info'] = '您的账号正在修改邮箱，请注意';
//        $emailService->sendDiyContactEmail($data, 1, $old_email);

        return Response::json(['code'=>200, 'message'=>'success']);
    }

    /**
     * 修改名称
     * @param Request $request
     * @return JsonResponse
     */
    public function changeFullName(Request $request){
        $userService = new UserService();
        $current_user = UserService::getCurrentUser($request);

        $full_name = $request->input('full_name');
        $full_name_result = $userService->validateFullName($full_name);
        if($full_name_result['code'] != 200){
            return Response::json(['code'=>500, 'message'=>$full_name_result['msg']]);
        }

        $userService->changeFullName($current_user, $full_name);
        return Response::json(['code'=>200, 'message'=>'success']);
    }

    /**
     * 获取账单信息
     * @param Request $request
     * @return Builder|Model|object|null
     */
    public function getBillingInfo(Request $request){
        $current_user = UserService::getCurrentUser($request);

        $billingService = new UserBillingInfoService();
        $info = $billingService->getByUserId($current_user->id);

        return Response::json(['code'=>200, 'message'=>'success', 'data'=>$info]);
    }

    /**
     * 修改账单信息
     * @param Request $request
     * @return JsonResponse
     */
    public function editBillingInfo(Request $request){
        $current_user = UserService::getCurrentUser($request);

        $validate = \Validator::make($request->all(), [
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email',
            'company'=>'required',
            'country'=>'required',
            'province'=>'required',
            'city'=>'required',
            'phone_number'=>'required|numeric',
            'address'=>'required',
            'zip'=>'required'
        ], [
                'first_name.required' => 'First Name is required.',
                'last_name.required' => 'Last Name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'company.required' => 'Company is required',
                'country.required' => 'Country is required',
                'province.required' => 'Province is required',
                'city.required' => 'City is required',
                'phone_number.required' => 'Phone Number is required',
                'phone_number.numeric' => 'Phone Number Numbers Only',
                'address.required' => 'Address is required',
                'zip.required' => 'Zip is required',]
        );

        if($validate->fails()){
            return Response::json(['code'=>500, 'message'=>$validate->messages()->first()]);
        }

        $first_name = ltrim(rtrim($request->input('first_name')));
        $last_name = ltrim(rtrim($request->input('last_name')));
        $email = trim($request->input('email'));
        $company = $request->input('company');
        $country = $request->input('country');
        $province = $request->input('province');
        $city = $request->input('city');
        $address = $request->input('address');
        $phone_number = $request->input('phone_number');
        $zip = $request->input('zip');

        $userBillingInfoService = new UserBillingInfoService();

        $userBillingInfoService->store($current_user->id, $first_name, $last_name, $email, $phone_number, $company, $country, $province, $city, $address, $zip);

        return Response::json(['code'=>200, 'message'=>'Success']);
    }

    /**
     * 忘记密码 发送邮件修改
     * @param Request $request
     * @return array|JsonResponse
     */
    public function forgetPassword(Request $request){
        $email = $request->input('email');

        if(!trim($email)){
            return Response::json(['code'=>500, 'message'=>'Required']);
        }

        $result  = filter_var($email, FILTER_VALIDATE_EMAIL);
        if(!$result){
            return ['code'=>500, 'message'=>'Please enter a valid email address.'];
        }

        if(!User::existsEmail($email)){
            return ['code'=>500, 'message'=>'Please enter a valid email address.'];
        }

        $userService = new UserService();
        $userService->sendChangePasswordEmail($email);

        return ['code'=>200, 'message'=>'Success.'];
    }

    /**
     * 修改密码 - 通过邮件方式
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function changePasswordByEmail(Request $request){
        $userService = new UserService();

        $token = $request->input('token');
        $new_password = str_replace(' ', '', $request->input('new_password'));
        $password_confirm = str_replace(' ', '', $request->input('password_confirm'));

        $password_result = $userService->validatePassword($new_password, 'en');
        if($password_result['code'] != 200){
            return Response::json(['code'=>500, 'message'=>$password_result['msg']]);
        }

        if($new_password != $password_confirm){
            return Response::json(['code'=>500, 'message'=>'The Password and Confirm Password do not match.']);
        }

        try {
            $payload = json_decode(decrypt($token));
        }catch (Exception $e){
            return Response::json(['code'=>500, 'message'=>'Invalid Token']);
        }

        $email = $payload->email;
        $alt = date('Y-m-d', $payload->alt);
        $expire_time = $payload->expire_time;

        //判断链接是否过期
        if(Carbon::parse($alt)->addHour(intval($expire_time)) < Carbon::now()){
            return Response::json(['code'=>500, 'message'=>'Expired Token']);
        }
        
        $user = User::where('email', $email)->first();

        //判断与旧密码是否一致
        if(User::encryptPassword($new_password) == $user->password){
            return Response::json(['code'=>500, 'message'=>'Cannot Match Old Password.']);
        }

        $userService->changePassword($user, $new_password);

        //删除用户token
        JWTService::forgetToken($email);

        return Response::json(['code'=>200, 'message'=>'success']);
    }

    /**
     * 注销账号
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function logout(Request $request){
        $current_user = UserService::getCurrentUser($request);

        //添加到注销用户列表
        if($current_user instanceof User){
            LogoutUser::addFromUser($current_user);
            try {
                $current_user->delete();
            } catch (Exception $e) {
                throw new Exception('system error');
            }

            //删除用户token
            JWTService::forgetToken($current_user->email);
        }

        return Response::json(['code'=>200, 'message'=>'success']);
    }

    /**
     * 退出登录接口
     * @param Request $request
     * @return JsonResponse
     */
    public function signOut(Request $request){
        $current_user = UserService::getCurrentUser($request);

        JWTService::forgetToken($current_user->email);

        return Response::json(['code'=>200, 'message'=>'success']);
    }
}