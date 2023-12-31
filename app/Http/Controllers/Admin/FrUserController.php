<?php


namespace App\Http\Controllers\Admin;

use App\Models\Mailmagicboard;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\User;
use App\Models\UserBillingInformation;
use App\Services\EmailService;
use App\Services\FrUserService;
use App\Services\JWTService;
use App\Services\SubscriptionService;
use App\Services\UserBillingInfoService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FrUserController extends BaseController
{

    public function list(Request $request)
    {
        $name = $request->input('name');
        $status = $request->input('status');
        $role = $request->input('role');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $field = $request->input('field');
        $export = $request->input('export', User::CODE_0_NO);

        $fruser = new FrUserService();
        $data = $fruser->getList($name, $start_date, $end_date, $status, $role, $export);
        if ($export) {
            $field = explode(',', $field);
            return $fruser->exportList($field, $data);
        }
        $status_arr = [1 => 'Not selected', 2 => 'Attend', 3 => 'Not attending'];
        $role_arr = [1 => 'user', 2 => 'speaker'];

        return $this->view('list')->with(['data' => $data, 'query' => $request->all(), 'status_arr' => $status_arr, 'role_arr' => $role_arr]);
    }

    public function import(Request $request)
    {
        $path = $request->file('file')->store('public/uploads');
        $arr = explode("/", $path);
        $storagePath = storage_path('app') . '/public/uploads/' . $arr[2];
        $spreadsheet = IOFactory::load($storagePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        $new_data = $str = [];
        $fruser = new FrUserService();
        if (count($data) > 0) {
            foreach ($data as $key => $val) {
                if ($key > 0) {
                    $str['name'] = $val[0];
                    $str['job_information_fr'] = $val[1];
                    $str['job_information_eng'] = $val[2];
                    if (!$fruser->checkOne($str)) {
                        $str['created_at'] = date("Y-m-d H:i:s", time());
                        $str['updated_at'] = date("Y-m-d H:i:s", time());
                        $str['uuid'] = $fruser->createUuid();
                        $new_data[] = $str;
                    }
                }
            }
            $fruser->add($new_data);
        }
        return ['code' => 200, 'msg' => 'success'];
    }

    public function edit($id)
    {
        $fruser = new FrUserService();
        $fruser_info = $fruser->getUserInfoById($id);
        $status_arr = [1 => 'Not selected', 2 => 'Attend', 3 => 'Not attending'];
        $role_arr = [1 => 'user', 2 => 'speaker'];
        return $this->view('edit')->with(['row' => $fruser_info, 'status_arr' => $status_arr, 'role_arr' => $role_arr]);
    }

    public function update($id, Request $request)
    {
        $data['name'] = trim($request->input('name'));
        $file = $request->file('file');
        if ($file) {
            $filePath = public_path('uploads');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($filePath, $fileName);
            $data['image'] = '/uploads/' . $fileName;
        }
        $data['job_information_eng'] = trim($request->input('job_information_eng'));
        $data['job_information_fr'] = trim($request->input('job_information_fr'));
        $data['status'] = $request->input('status');
        $data['role'] = $request->input('role');
        $data['updated_at'] = date('Y-m-d H:i:s', time());

        $fruser = new FrUserService();
        $fruser->update($id, $data);

        return ['code' => 200, 'msg' => 'success'];
    }

    public function softDel()
    {
        $param = request()->input();
        $id = $param['id'];
        if (!empty($id)) {
            $fruser = new FrUserService();
            $row = $fruser->update($param['id'], ['is_delete' => 2, 'updated_at' => date('Y-m-d H:i:s')]);
            if (1 == $row) {
                $data['code'] = 0;
                flash('Deletion successful')->success()->important();
            } else {
                $data['code'] = 1;
                $data['msg'] = 'Delete failed.';
                flash('Delete failed.')->error()->important();
            }
        } else {
            $data['code'] = 1;
            $data['msg'] = 'Invalid parameters, please try again.';
        }
        return $data;
    }

    public function sendRegistrationEmail($id)
    {
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
    public function logoutList()
    {
        $userService = new UserService();
        $data = $userService->getLogoutList();
        return $this->view('logout')->with(['type_arr' => User::$typeArr, 'data' => $data]);
    }
}