<?php


namespace App\Services;


use App\Export\UserExport;
use App\Models\BackGroundUser;
use App\Models\BackGroundUserRemain;
use App\Models\FrMeeting;
use App\Models\FrUser;
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
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;
use Response;
use Maatwebsite\Excel\Facades\Excel;

class FrMeetingService
{

    public function getList($topic, $date,$speaker)
    {
        $query = FrMeeting::query();
        if ($topic) {
            $query->where(function ($item) use ($topic) {
                $item->where('topic_fr', 'like', "%" . $topic . "%")
                    ->orWhere('topic_eng', 'like', "%" . $topic . "%");
            });
        }
        if ($date) {
            $query->where(function ($item) use ($date) {
                $item->where('start_time', '>=', $date . " 00:00:00 ")
                    ->where('end_time', '<=', $date . " 23:59:59 ");
            });
        }
        if ($speaker) {
            $query->where(function ($item) use ($speaker) {
                $item->where('user_id', 'like', $speaker . ",%")
                    ->orWhere('user_id', 'like', "%," . $speaker . ",%")
                    ->orWhere('user_id', 'like', "%," . $speaker)
                    ->orWhere('user_id',$speaker);
            });
        }
        $query->orderBy('created_at', 'desc');
        $data = $query->paginate(10);
        foreach ($data as $key => $value) {
            $data[$key]['speaker_info'] = [];
            if ($value['user_id']) {
                $users = explode(',', $value['user_id']);
                if (count($users) == 1) {
                    $infos = FrUser::query()->where('id', $users)->select('name', 'job_information_eng', 'job_information_fr')->get()->toArray();
                } else {
                    $infos = FrUser::query()->whereIn('id', $users, 'or')->select('name', 'job_information_eng', 'job_information_fr')->get()->toArray();
                }
                $data[$key]['speaker_info'] = $infos;
            }
        }
        return $data;
    }

    public function createUuid()
    {
        $uuid = \Illuminate\Support\Str::random(16);
        $num = FrUser::where('uuid', $uuid)->count();
        if ($num == 1) {
            $uuid = $this->createUuid();
        }
        return $uuid;
    }

    public function getUserInfoById($id)
    {
        return DB::table('fr_users')->find($id);
    }

    /**
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        return DB::table('fr_users')->insert($data);
    }

    public function update($id, $data)
    {
        return DB::table('fr_users')->where('id',$id)->update($data);
    }

    public function checkOne($str)
    {
        $result = DB::table('fr_users')->where($str)->first();
        return !empty($result) ? 1 : 0;
    }

    /**
     * 更新用户
     * @param $id
     * @param $email
     * @param $full_name
     */
//    public function update($id, $email, $full_name){
//        $user = User::find($id);
//
//        if($user instanceof User){
//            $user->email = $email;
//            $user->full_name = $full_name;
//            $user->save();
//        }
//    }

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

    public function exportList($fields, $data)
    {
        $map = [
            'id' => 'ID',
            'name' => 'Name',
            'job_information_eng' => 'Information(eng)',
            'job_information_fr' => 'Information(fr)',
            'uuid' => 'uuid',
            'role' => 'Role',
            'status' => 'Status',
        ];
        $status_arr = [1 => 'Not selected', 2 => 'Attend', 3 => 'Not attending'];
        $role_arr = [1 => 'user', 2 => 'speaker'];

        $header = [];
        foreach ($fields as $field) {
            $header[] = array_get($map, $field);
        }
        $result[] = $header;
        foreach ($data as $value) {
            $rows = [];
            foreach ($fields as $field) {
                $content = array_get($value, $field);

                if ($field == 'role') {
                    $content = array_get($role_arr, $content);
                }

                if ($field == 'status') {
                    $content = array_get($status_arr, $content);
                }

                $rows[] = $content;
            }
            $result[] = $rows;
        }
        $userExport = new UserExport($result);
        $fileName = 'users' . time() . '.xlsx';
        return Excel::download($userExport, $fileName);
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
        return User::where('email', $email)->where('is_verify', User::IS_VERIFY_2_YES)->first();
    }

    /**
     * 获取当前用户
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|Model|object
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
     * 发送 修改密码 的邮件
     * @param $email
     * @param $name
     * @param $source
     */
    public function sendChangePasswordEmail($email, $name, $source = User::SOURCE_1_SDK){
        $tags = "forget-password:$email";
        $token = CommonService::getTokenByEmail($email);

        //缓存：先清除这个邮箱标记的token
        \Cache::tags($tags)->flush();
        $expire_time = Carbon::now()->addDay();
        \Cache::tags($tags)->put($token, $email, $expire_time);

        //主站地址
        $website = env('WEB_HOST');
        //SAAS官网地址
        $website_saas = env('WEB_HOST_SAAS');
        //重置密码路由
        $reset_url = env('WEB_HOST_SAAS') . '/reset/password?token=' . $token;

        $email_model = Mailmagicboard::getByName($name);
        $emailService = new EmailService();
        $data['title'] = $email_model->title;
        $data['info'] = $email_model->info;
        $data['id'] = $email_model->id;
        $data['info'] = str_replace("#@website", $website, $data['info']);
        $data['info'] = str_replace("#@saas_site", $website_saas, $data['info']);
        $data['info'] = str_replace("#@reset_url", $reset_url, $data['info']);

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
        }elseif($old_type == User::TYPE_2_SDK_TRY_OUT || $old_type == User::TYPE_6_SAAS_TRY_OUT){
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

    public function sendVerifyEmail($email, $name){
        //主站官网地址
        $website = env('WEB_HOST');
        //SAAS官网地址
        $website_saas = env('WEB_HOST_SAAS');

        $token_suffix = 'verify-email';
        $token = CommonService::getTokenByEmail($email);

        //缓存：先清除这个邮箱标记的token
        \Cache::tags($token_suffix . ':' . $email)->flush();
        $expire_time = Carbon::now()->addDay();
        \Cache::tags($token_suffix . ':' . $email)->put($token, $email, $expire_time);

        $login_url = $website_saas . '/login?token=' . $token;
        //发送邮件
        $email_model = Mailmagicboard::getByName($name);
        $emailService = new EmailService();
        $data['title'] = $email_model->title;
        $data['info'] = $email_model->info;
        $data['id'] = $email_model->id;
        $data['info'] = str_replace("#@website", $website, $data['info']);
        $data['info'] = str_replace("#@saas_site", $website_saas, $data['info']);
        $data['info'] = str_replace("#@login_url", $login_url, $data['info']);

        $emailService->sendDiyContactEmail($data, 0, $email);
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

    /**
     * 获取用户资产 新
     * @param $user_id
     * @return array
     */
    public function getRemainByUser($user_id){
        $tenant_id = BackGroundUser::query()
            ->where('compdfkit_id', $user_id)
            ->value('tenant_id');

        return BackGroundUserRemain::query()
            ->where('tenant_id', $tenant_id)
            ->get()
            ->keyBy('asset_type')
            ->toArray();
    }
}