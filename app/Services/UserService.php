<?php


namespace App\Services;


use App\Export\UserExport;
use App\Models\LogoutUser;
use App\Models\Mailmagicboard;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAssets;
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
        $query = User::leftJoin('user_billing_information as b', 'b.user_id', '=', 'users.id')
            ->leftJoin('orders', function ($join){
                $join->on('users.id', '=', 'orders.user_id')
                    ->whereIn('status', [1, 2]);
            })
            ->leftJoin('email_blacklist', 'users.email', '=', 'email_blacklist.email')
            ->selectRaw('users.id as uid, users.email as u_email, users.full_name as full_name, users.order_num, users.order_amount, users.type as type, users.created_at as register_time, email_blacklist.id as black_id, SUM(orders.price) as order_amount, COUNT(orders.id) as order_num, b.company, b.country')
            ->groupBy('users.id');

        if ($keyword) {
            $query->where(function ($item) use ($keyword) {
                $item->where('users.id', intval($keyword))
                    ->orWhere('b.company', $keyword)
                    ->orWhere('users.email', $keyword)
                    ->orWhere('users.full_name', $keyword);
            });
        }

        if ($country && $country != 'All') {
            $query->where('b.country', $country);
        }

        if ($type && $type != -1) {
            $query->where('users.type', intval($type));
        }

        if ($start_date) {
            $query->where('users.created_at', '>=', Carbon::parse($start_date)->startOfDay()->toDateTimeString());
        }

        if ($end_date) {
            $query->where('users.created_at', '<=', Carbon::parse($end_date)->endOfDay()->toDateTimeString());
        }

        $query->orderBy('users.created_at', 'desc');

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
     * @param $source
     */
    public function add($email, $full_name, $password, $source)
    {
        $user = New User();
        $user->email = $email;
        $user->full_name = $full_name;
        $user->password = $password;
        $user->source = $source;

        if($source == User::SOURCE_2_SAAS){
            $user->type = User::TYPE_6_SAAS_TRY_OUT;
        }

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
            'unique' => ['zn'=>'该邮箱已注册', 'en'=>'The Email is already in use.']
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
     * @param $source
     */
    public function sendChangePasswordEmail($email, $name, $source = User::SOURCE_1_SDK){
        $server_name = $server = env('WEB_HOST') . '/reset/password';

        //发送邮件时间
        $payload = ['email' => $email, 'alt'=>time(), 'expire_time' => 24];
        $token = 'forget-password:' . encrypt(json_encode($payload));

        //缓存
        $expire_time = Carbon::now()->addDay();
        \Cache::put($token, $email, $expire_time);

        $server .= '?token=' . $token;
        $url = "<a href='$server'>$server_name</a>";
        //发送邮件
        $email_model = Mailmagicboard::getByName($name);
        $emailService = new EmailService();
        $data['title'] = $email_model->title;
        $data['info'] = $email_model->info;
        $data['id'] = $email_model->id;
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

    /**
     * 修改用户类型
     * @param $type 2：试用订单 3：SaaS订单 4：SDK订单 5：SaaS和SDK订单
     * @param $user_id
     */
    public function changeType($type, $user_id){
        $user = User::find($user_id);

        $old_type = $user->type;
        //已经是SaaS和SDK用户不改变类型
        if($old_type == User::TYPE_5_SAAS_ADN_SDK){
            return;
        }

        //订单为SaaS和SDK 则直接更改为 SaaS、SDK用户
        if($type == User::TYPE_5_SAAS_ADN_SDK){
            $user->type = $type;
        }

        if($old_type == User::TYPE_1_FREE){
            //免费用户直接更改为新类型
            $user->type = $type;
        }elseif($old_type == User::TYPE_2_SDK_TRY_OUT){
            if($type > 2){
                //试用用户，如果是购买则更改为新类型
                $user->type = $type;
            }
        }elseif($old_type == User::TYPE_3_SAAS && $type == User::TYPE_4_SDK){
            //SaaS用户如果购买了SDK更改为 SaaS、SDK用户
            $user->type = User::TYPE_5_SAAS_ADN_SDK;
        }elseif($old_type == User::TYPE_4_SDK && $type == User::TYPE_3_SAAS){
            //SDK用户如果购买了SaaS更改为 SaaS、SDK用户
            $user->type = User::TYPE_5_SAAS_ADN_SDK;
        }

        $user->save();
    }

    /**
     * 获取用户账单
     * @param $user_id
     * @param $details_type
     * @return \Illuminate\Database\Eloquent\Builder|Model|Builder|object|null
     */
    public function getOrderTotalByUser($user_id, $details_type){
        return Order::where('user_id', $user_id)
            ->whereIn('details_type', $details_type)
            ->whereIn('status', [1,2])
            ->groupBy('user_id')
            ->selectRaw('sum(price) as order_amount, count(user_id) as order_num')
            ->first();
    }

    /**
     * 获取用户资产
     * @param $user_id
     * @return array
     */
    public function getSaaSAssetByUser($user_id){
        return UserAssets::where('user_id', $user_id)->where('status', UserAssets::STATUS_1_ENABLE)
            ->get()
            ->toArray();
    }
}