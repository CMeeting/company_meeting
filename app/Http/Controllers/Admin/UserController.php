<?php


namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Services\EmailService;
use App\Services\JWTService;
use App\Services\UserBillingInfoService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;

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

        return $this->view('list')->with(['type_arr' => User::$typeArr, 'data' => $data, 'query' => $request->all()]);
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
        $full_name = $request->input('full_name');

        $full_name = ltrim($full_name);
        $full_name = rtrim($full_name);

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

        //TODO 发送邮件
        $emailService = new EmailService();
        $data['title'] = 'ComPDFKit添加成功';
        $data['info'] = '您的密码为' . $password . '请及时修改';
        $emailService->sendDiyContactEmail($data, 1, $email);

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

            //TODO 发送邮件提醒
            $emailService = new EmailService();
            $data['title'] = 'ComPDFKit邮箱修改';
            $data['info'] = '请注意邮箱已修改';
            $emailService->sendDiyContactEmail($data, 1, $email);
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
        //用户信息（基本信息，统计信息）
        $userService = new UserService();
        $user = $userService->getUserStatistics($id);

        //账单信息
        $billingService = new UserBillingInfoService();
        $billing = $billingService->getByUserId($id);

        //订单信息
        $orders = $userService->getUserOrders($id);

        //TODO 后续声明常量
        $status_arr = [0 => '待支付', 1 => '已付款', 2 => '已完成', 3 => '待退款', 4 => '已关闭'];
        $pay_type_arr = [1 => 'paddle', 2 => '支付宝', 3 => '微信', 4 => '不需要支付'];
        $source_arr = [1 => '后台创建', 2 => '用户购买'];

        return $this->view('detail')->with(['user' => $user, 'billing' => $billing, 'orders' => $orders, 'status_arr' => $status_arr, 'pay_type_arr' => $pay_type_arr, 'source_arr' => $source_arr]);
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

        $userService->sendChangePasswordEmail($email);

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