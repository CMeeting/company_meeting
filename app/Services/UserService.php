<?php


namespace App\Services;


use App\Export\UserExport;
use App\Models\LogoutUser;
use App\Models\User;
use App\Models\UserLoginLog;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class UserService
{
    /**
     * 用户列表页
     * @param $keyword
     * @param $country
     * @param $type
     * @param $start_date
     * @param $end_date
     * @param $export
     * @return mixed
     */

    public function getList($keyword, $country, $type, $start_date, $end_date, $export)
    {
        $query = \DB::table('users as u')
            ->leftJoin('user_billing_information as b', 'u.id', '=', 'b.user_id')
            ->leftJoin('orders as o', function ($join) {
                $join->on('u.id', '=', 'o.mid')
                    ->where('o.status', '!=', 3)
                    ->where('o.status', '!=', 4);
            })
            ->select(['u.id as uid', 'u.email as u_email', 'u.full_name as full_name', 'u.type as type', 'u.created_at as register_time', 'b.*'])
            ->selectRaw('count(o.id) as order_number')
            ->selectRaw('sum(o.pay_price) as order_price')
            ->groupBy('u.id');

        if ($keyword) {
            $query->where(function ($item) use ($keyword) {
                $item->where('u.id', intval($keyword))
                    ->orWhere('b.company', $keyword)
                    ->orWhere('u.email', $keyword)
                    ->orWhere('u.full_name', $keyword);
            });
        }

        if ($country && $country != 'All') {
            $query->where('b.country', $country);
        }

        if ($type && $type != -1) {
            $query->where('u.type', intval($type));
        }

        if ($start_date) {
            $query->where('u.created_at', '>=', Carbon::parse($start_date)->startOfDay()->toDateTimeString());
        }

        if ($end_date) {
            $query->where('u.created_at', '<=', Carbon::parse($end_date)->endOfDay()->toDateTimeString());
        }

        $query->orderBy('u.created_at', 'desc');

        if($export == User::CODE_1_YES){
            return $query->get()->toArray();
        }else{
            return $query->paginate(10);
        }
    }

    /**
     * 添加用户
     * @param $email
     * @param $full_name
     * @param $password
     */
    public function add($email, $full_name, $password)
    {
        $user = New User();
        $user->email = $email;
        $user->full_name = $full_name;
        $user->password = encrypt($password);

        $user->save();

        return $user->id;
    }

    /**
     * 更新用户
     * @param $id
     * @param $email
     * @param $full_name
     */
    public function update($id, $email, $full_name){
        $user = User::find($id);

        if($user instanceof User){
            $user->email = $email;
            $user->full_name = $full_name;
            $user->save();
        }
    }

    /**
     * 根据id返回用户详情
     * @param $id
     * @return User|User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getById($id)
    {
        return User::find($id);
    }

    /**
     * 用户汇总信息
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Model|Builder|object|null
     */
    public function getUserStatistics($user_id)
    {
        return \DB::table('users')
            ->leftJoin('orders', 'users.id', '=', 'orders.mid')
            ->leftJoin('user_login_logs', 'users.id', '=', 'user_login_logs.user_id')
            ->select(['users.*'])
            ->selectRaw('count(orders.id) as order_number')
            ->selectRaw('sum(orders.pay_price) as order_price')
            ->selectRaw('count(user_login_logs.id) as login_times')
            ->groupBy('users.id')
            ->where('users.id', $user_id)
            ->first();
    }

    /**
     * 用户订单信息
     * @param $user_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserOrders($user_id)
    {
        return \DB::table('orders as o')
            ->leftJoin('orders_goods as og', 'o.id', '=', 'og.order_id')
            ->where('o.mid', $user_id)
            ->select(['o.*'])
            ->selectRaw('group_concat(DISTINCT  og.goods_name) as good_name')
            ->groupBy('o.id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * 导出
     * @param $fields
     * @param $data
     * @return array
     */
    public function exportList($fields, $data){
        $map = [
            'uid' => '用户ID',
            'u_email' => 'Email',
            'full_name' => 'Full Name',
            'company' => 'Company',
            'country' => 'Country',
            'type' => '用户类型',
            'order_price' => '消费金额',
            'order_number' => '订单数量',
            'register_time' => '注册时间',
        ];

        $header = [];
        foreach ($fields as $field){
            $header[] = array_get($map, $field);
        }
        $result[] = $header;

        foreach ($data as $value){
            $rows = [];
            foreach ($fields as $field){
                $content = $value->$field;

                if($field == 'type'){
                    $content = array_get(User::$typeArr, $content);
                }

                $rows[] = $content;
            }
            $result[] = $rows;
        }

        $userExport = new UserExport($result);
        $fileName = 'export'. DIRECTORY_SEPARATOR .'用户列表' . time() . '.xlsx';
        \Excel::store($userExport, $fileName);

        //ajax请求 需要返回下载地址，在使用location.href请求下载地址
        return ['url'=>route('download', ['file_name'=>$fileName])];
    }

    /**
     * 邮箱检验
     * @param $email
     * @param string $lang
     * @param null $id
     * @return array
     */
    public function validateEmail($email, $lang = 'zn', $id = null){
        $message = [
            'required'=> ['zn'=>'邮箱不能为空', 'en'=>'Email is required.'],
            'format' => ['zn'=>'请输入有效邮箱', 'en'=>'Please enter a valid email address.'],
            'unique' => ['zn'=>'输入的邮箱已被占用', 'en'=>'The Email is already in use.']
        ];

        if(!$email){
            return ['code'=>500, 'msg'=>array_get($message, "required.$lang")];
        }

        $result  = filter_var($email, FILTER_VALIDATE_EMAIL);
        if(!$result){
            return ['code'=>500, 'msg'=>array_get($message, "format.$lang")];
        }

        if(User::existsEmail($email, $id)){
            return ['code'=>500, 'msg'=>array_get($message, "unique.$lang")];
        }

        return ['code'=>200, 'msg'=>'success'];
    }

    /**
     * 名称检验
     * @param $full_name
     * @param string $lang
     * @return array
     */
    public function validateFullName($full_name, $lang = 'zn'){
        $message = [
            'required' => ['zn'=>'Full Name不能为空', 'en'=>'Full Name is required.'],
            'format' => ['zn'=>'Full Name长度需在1-24之间', 'en'=>'Full Name must between 1-24 characters.']
        ];

        if(!$full_name){
            return ['code'=>500, 'msg'=>array_get($message, "required.$lang")];
        }

        $len = strlen($full_name);
        if($len < 1 || $len > 24){
            return ['code'=>500, 'msg'=>array_get($message, "format.$lang")];
        }

        return ['code'=>200, 'msg'=>'success'];
    }

    /**
     * 密码校验
     * @param $password
     * @param string $lang
     * @return array
     */
    public function validatePassword($password, $lang = 'zn'){
        $message = [
            'required' => ['zn'=>'密码不能为空', 'en'=>'Password is required.'],
            'format' => ['zn'=>'密码长度需在6-24之间', 'en'=>'Password must between 6-24 characters.']
        ];

        if(!$password){
            return ['code'=>500, 'msg'=>array_get($message, "required.$lang")];
        }

        $len = strlen($password);
        if($len < 1 || $len > 24){
            return ['code'=>500, 'msg'=>array_get($message, "format.$lang")];
        }

        return ['code'=>200, 'msg'=>'success'];
    }

    /**
     * 根据邮箱获取用户
     * @param $email
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public static function getByEmail($email){
        return User::where('email', $email)->first();
    }

    /**
     * 获取登录用户
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public static function getCurrentUser(Request $request){
        $email = $request->input('login_user_email');

        return self::getByEmail($email);
    }

    /**
     * 修改密码
     * @param $user
     * @param $password
     */
    public function changePassword(User $user, $password){
        $user->password = encrypt($password);
        $user->save();
    }

    /**
     * 修改邮箱
     * @param User $user
     * @param $email
     */
    public function changeEmail(User $user, $email){
        $user->email = $email;
        $user->save();
    }

    /**
     * 修改全名
     * @param User $user
     * @param $full_name
     */
    public function changeFullName(User $user, $full_name){
        $user->full_name = $full_name;
        $user->save();
    }

    /**
     * 发送修改密码的邮件
     * @param $email
     */
    public function sendChangePasswordEmail($email){
        //发送邮件时间
        $payload = ['email' => $email, 'alt'=>time(), 'expire_time' => 24];
        $token = encrypt(json_encode($payload));
        //TODO 发送邮件
//        $emailService = new EmailService();
//        $data['title'] = '注册成功';
//        $data['info'] = '<a href="">点击这个链接修改密码</a>';
//        $emailService->sendDiyContactEmail($data, 1, $email);
    }

    /**
     * 注销用户列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLogoutList(){
        return LogoutUser::paginate(10);
    }

    /**
     * 增加登录次数
     * @param $user_id
     */
    public function addLoginTime($user_id){
        $login_model = new UserLoginLog();
        $login_model->user_id = $user_id;
        $login_model->save();
    }
}