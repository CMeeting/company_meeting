<?php


namespace App\Http\Controllers\Admin;


use App\Models\Mailmagicboard;
use App\Models\User;
use App\Services\EmailService;
use App\Services\JWTService;
use App\Services\SubscriptionService;
use App\Services\UserBillingInfoService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * 列表
     * @param Request $request
     * @return  mixed
     */
    public function list(Request $request)
    {
        $keyword = $request->input('keyword');
        $country = $request->input('country');
        $type = $request->input('type');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $field = $request->input('field');
        $export = $request->input('export', User::CODE_0_NO);

        $userService = new UserService();
        $data = $userService->getList($keyword, $country, $type, $start_date, $end_date, $export);
        if ($export == User::CODE_1_YES) {
            $field = explode(',', $field);
            if (empty($field)) {
                return ['code' => 500, 'msg' => '导出列不能为空'];
            }
            return $userService->exportList($field, $data);
        }

        $status_arr = [0 => '待支付', 1 => '已付款', 2 => '已完成', 3 => '待退款', 4 => '已关闭'];

        return $this->view('list')->with(['type_arr' => User::$typeArr, 'data' => $data, 'query' => $request->all(), 'status_arr' => $status_arr]);
    }

    /**
     * 添加用户视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return $this->view('create');
    }

    /**
     * 新增用户
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $email = trim($request->input('email'));
        $full_name = trim($request->input('full_name'));

        $userService = new UserService();

        $result_email = $userService->validateEmail($email);
        if ($result_email['code'] != 200) {
            return $result_email;
        }

        $result_full_name = $userService->validateFullName($full_name);
        if ($result_full_name['code'] != 200) {
            return $result_full_name;
        }

        //生成随机密码
        $password = User::getRandStr();

        $userService = new UserService();
        $userService->add($email, $full_name, $password);

        //自动订阅电子报
        $subsService = new SubscriptionService();
        $subsService->update_status(['email'=>$email, 'subscribed'=>1]);

        //发送邮件
        $url = env('WEB_HOST') . '/personal';
        $emailModel = Mailmagicboard::getByName('新增用户');
        $emailService = new EmailService();
        $data['title'] = $emailModel->title;
        $data['info'] = $emailModel->info;
        $data['info'] = str_replace("#@username", $full_name, $data['info']);
        $data['info'] = str_replace("#@mail", $email, $data['info']);
        $data['info'] = str_replace("#@password", $password, $data['info']);
        $data['info'] = str_replace("#@url", $url, $data['info']);

        $emailService->sendDiyContactEmail($data, 0, $email);

        return ['code' => 200, 'msg' => 'success'];
    }

    /**
     * 编辑视图
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $userService = new UserService();
        $user = $userService->getById($id);

        return $this->view('edit')->with(['row' => $user]);
    }

    /**
     * 更新用户资料
     * @param $id
     * @param Request $request
     * @return array
     */
    public function update($id, Request $request)
    {
        $email = trim($request->input('email'));
        $full_name = $request->input('full_name');

        $full_name = ltrim($full_name);
        $full_name = rtrim($full_name);

        $userService = new UserService();

        $email_result = $userService->validateEmail($email, 'zn', $id);
        if ($email_result['code'] != 200) {
            return $email_result;
        }

        $full_name_result = $userService->validateFullName($full_name);
        if ($full_name_result['code'] != 200) {
            return $full_name_result;
        }

        $old_email = User::find($id)->email;

        $userService->update($id, $email, $full_name);

        if($old_email != $email){
            //删除token缓存
            JWTService::forgetToken($old_email);

            //自动订阅电子报
            $subsService = new SubscriptionService();
            $subsService->update_status(['email'=>$email, 'subscribed'=>1]);

            $emailService = new EmailService();
            //编辑用户资料提示新邮箱
            $emailModelNew = Mailmagicboard::getByName('编辑用户资料提示新邮箱');
            $data['title'] = $emailModelNew->title;
            $data['info'] = $emailModelNew->info;
            $data['info'] = str_replace("#@old_mail", $old_email, $data['info']);
            $data['info'] = str_replace("#@new_mail", $email, $data['info']);
            $url = env('WEB_HOST') . '/unsubscribe?email=' . $email;
            $data['info'] = str_replace("#@url", $url, $data['info']);
            $emailService->sendDiyContactEmail($data, 0, $email);

            //编辑用户资料提示老邮箱
            $emailModelOld = Mailmagicboard::getByName('编辑用户资料提示老邮箱');
            $data['title'] = $emailModelOld->title;
            $data['info'] = $emailModelOld->info;
            $data['info'] = str_replace("#@new_mail", $email, $data['info']);
            $emailService->sendDiyContactEmail($data, 0, $old_email);
        }

        return ['code' => 200, 'msg' => 'success'];
    }

    /**
     * 用户详情
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($id)
    {
        //用户信息（基本信息）
        $userService = new UserService();
        $user = $userService->getUserById($id);

        //账单信息
        $billingService = new UserBillingInfoService();
        $billing = $billingService->getByUserId($id);

        //订单信息
        $orders = $userService->getUserOrders($id);

        //统计信息
        $total_info = $userService->getOrderTotalByUser($id);
        if($total_info){
            $total_info = $total_info->toArray();
        }else{
            $total_info = ['order_amount' => 0.00, 'order_num' => 0];
        }

        //TODO 后续声明常量
        $status_arr = [0 => '待付款', 1 => '已付款', 2 => '已完成', 3 => '待退款', 4 => '已关闭'];
        $pay_type_arr = [1 => 'paddle', 2 => '支付宝', 3 => '微信', 4 => '不需要支付'];
        $source_arr = [1 => '后台创建', 2 => '用户购买'];
        $details_type_arr = [1 => 'SDK试用', 2 => 'SDK订单', 3 => 'SaaS订单'];

        return $this->view('detail')->with(['user' => $user, 'billing' => $billing, 'orders' => $orders, 'total_info'=>$total_info, 'status_arr' => $status_arr, 'pay_type_arr' => $pay_type_arr, 'source_arr' => $source_arr, 'details_type_arr'=>$details_type_arr]);
    }

    /**
     * 重置密码 -发送邮件
     * @param $id
     * @return array
     */
    public function resetPassword($id){
        $userService = new UserService();
        $user = $userService->getById($id);

        $email = $user->email;
        $userService->sendChangePasswordEmail($email, '后台重置用户密码');

        return ['code' => 200, 'msg' => 'success'];
    }

    /**
     * 注销用户列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function logoutList(){
        $userService = new UserService();
        $data = $userService->getLogoutList();
        return $this->view('logout')->with(['type_arr' => User::$typeArr, 'data' => $data]);
    }
}