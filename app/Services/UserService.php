<?php


namespace App\Services;


use App\Export\UserExport;
use App\Models\LogoutUser;
use App\Models\Mailmagicboard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;
use Maatwebsite\Excel\Facades\Excel;

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
            ->select(['u.id as uid', 'u.email as u_email', 'u.full_name as full_name', 'u.order_num', 'u.order_amount','u.type as type', 'u.created_at as register_time', 'b.*'])
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
        $user->password = $password;

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
     * @return User|User[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function getById($id)
    {
        return User::find($id);
    }

    /**
     * 用户汇总信息
     * @param $user_id
     * @return Model|Builder|object|null
     */
    public function getUserById($user_id)
    {
        return User::find($user_id);
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
            ->where('o.user_id', $user_id)
            ->select(['o.*'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    /**
     * 导出
     * @param $fields
     * @param $data
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportList($fields, $data){
        $map = [
            'uid' => '用户ID',
            'u_email' => 'Email',
            'full_name' => 'Full Name',
            'company' => 'Company',
            'country' => 'Country',
            'type' => '用户类型',
            'order_amount' => '消费金额',
            'order_num' => '订单数量',
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
        $fileName = '用户列表' . time() . '.xlsx';
        return  Excel::download($userExport, $fileName);
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
//            'format' => ['zn'=>'Full Name长度需在1-24之间', 'en'=>'Full Name must between 1-24 characters.']
        ];

        if(!$full_name){
            return ['code'=>500, 'msg'=>array_get($message, "required.$lang")];
        }

//        $len = strlen($full_name);
//        if($len < 1 || $len > 24){
//            return ['code'=>500, 'msg'=>array_get($message, "format.$lang")];
//        }

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
     * @return \Illuminate\Database\Eloquent\Builder|Model|object
     */
    public static function getByEmail($email){
        return User::where('email', $email)->first();
    }

    /**
     * 获取当前用户
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|Model|JsonResponse|object
     */
    public static function getCurrentUser(Request $request){
        $email = $request->input('login_user_email');
        $user = self::getByEmail($email);

        if(!$user instanceof User){
            return Response::json(['code'=>500, 'message'=>'System Error']);
        }
        return $user;

    }

    /**
     * 修改密码
     * @param $user
     * @param $password
     */
    public function changePassword(User $user, $password){
        $user->password = $password;
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
     * @param $name
     */
    public function sendChangePasswordEmail($email, $name){
        $server_name = $server = env('WEB_HOST') . '/reset/password';

        //发送邮件时间
        $payload = ['email' => $email, 'alt'=>time(), 'expire_time' => 24];
        $token = encrypt(json_encode($payload));

        $server .= '?token=' . $token;
        $url = "<a href='$server'>$server_name</a>";
        //发送邮件
        $email_model = Mailmagicboard::getByName($name);
        $emailService = new EmailService();
        $data['title'] = $email_model->title;
        $data['info'] = $email_model->info;
        $data['info'] = str_replace("#@url", $url, $data['info']);

        $emailService->sendDiyContactEmail($data, 0, $email);
    }

    /**
     * 注销用户列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLogoutList(){
        return LogoutUser::paginate(10);
    }
}