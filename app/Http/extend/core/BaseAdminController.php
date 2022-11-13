<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/2 0002
 * Time: 下午 6:07
 */

namespace core;


use app\api\service\SysConf;
use core\helper\ArrayHelper;
use core\helper\JsonHelper;
use core\helper\LogHelper;
use core\helper\SysHelper;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use think\App;
use think\Controller;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;


class BaseAdminController extends Controller
{
    /**
     * 接受参数
     * @var mixed
     */
    public $param;
    /**
     * token扩展参数
     * @var
     */
    public $ext;
    /**
     * token发布者
     * @var
     */
    public $website;
    /**
     * 用户信息
     * @var
     */
    public $userInfo = [];
    /**
     * jwt配置
     * @var
     */
    private $jwt;

    /**
     * BaseAdminController constructor.
     * @param App|null $app
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->_initConf();
        $this->param = Request::param();
        ArrayHelper::filterTrim($this->param);
        $this->view->engine->layout('layout/admin');
        if (!empty(Session::get('token'))) {
            $this->assign('admin_user', $this->getUserInfo());
        }
        $database = Config::get()['database']['database'];
        if (SysConf::getValue('admin_enable_edit')) {
            $this->assign('enable_edit', true);
        };
        $this->assign('database', $database);
    }

    /**
     * @return mixed
     */
    public function getUserInfo($bool = false)
    {
        $token = Session::get('token');
        if(empty($token)){
            $this->isLogin();
        }
        try{
            $token = JWT::decode($token, $this->jwt['key'], ['HS256']);
        }catch (ExpiredException $e){
            Session::set("token", '');
            $this->redirect('/admin/login');
        }
        $this->userInfo = JsonHelper::decode(JsonHelper::encode($token))['userInfo'];
        $this->isLogin();
        if($bool){
            $this->isAdmin();
        }
        return $this->userInfo;
    }

    /**
     * 接受参数
     * @param array $data
     * @return mixed
     */
    public function getRequstParam(array $data = [], $layout = true)
    {
        if (isset($this->param['__token__'])) {
            $rule = ['__token__' => 'token',];
            $check = ['__token__' => $this->param['__token__']];
            $msg = ['__token__' => '请不要刷新该页面！',];
            //引入验证类
            $validate = Validate::make($rule, $msg);
            if (!$validate->check($check)) {
                $length = strripos($_SERVER['REQUEST_URI'], "/") + 1;
                $this->error($validate->getError(), url(substr($_SERVER['REQUEST_URI'], 0, $length)), '', 1);
            }
            unset($this->param['__token__']);
        }
        if (!$layout) {
            $this->view->engine->layout('layout/empty');
        } else {
            if (empty(Session::get('token'))) {
                $this->isLogin();
            }
        }
        foreach ($data as $value) {
            $bool = array_key_exists($value, $this->param);

            if (false === $bool) {
                iF (Config::get('app.app_debug')) {
                    $this->failure("参数" . $value . "有误");
                } else {
                    $this->failure("参数有误");
                }
            }
        }
        $this->assign('param', $this->param);
        return $this->param;
    }

    /**
     * 重载成功方法
     * @param null $data
     * @param string $msg
     * @param string $url
     * @param int $w
     * @param array $header
     */
    protected function success($data = null, $msg = "", $url = '', $w = 0, array $header = [])
    {
        if (empty($data)) {
            $data = '';
        }
        $this->_response($data, $msg);
    }

    /**
     * 请求失败方法
     * @param string $servicekey
     * @param null $data
     */
    protected function failure($servicekey = '', $data = null)
    {
        if (empty($data)) {
            $data = '';
        }
        $code = '1000';
        $serviceCode = SysHelper::getServiceCode($servicekey);
        if (!empty($serviceCode)) {
            $msg = $serviceCode['message'];
            $code = $serviceCode['code'];
        } else {
            $msg = $servicekey;
        }
        $this->_response($data, $msg, $code);
    }

    /**
     * @param $data
     * @param string $message
     * @param string $code
     */
    private function _response($data, $message = '', $code = '0')
    {
        $result['code'] = $code;
        $result['message'] = $message;
        $result['data'] = $data;
        echo JsonHelper::encode($result);
        exit;
    }

    /**
     * 初始化jwt函数
     */
    private function _initConf()
    {
        $jwt = SysHelper::getEnv('jwt');

        if (empty($jwt)) {
            $this->failure('jwt_not_exists');
        }
        if (!isset($jwt['key'])) {
            $this->failure('jwt_key_not_exists');
        }
        $this->jwt = $jwt;            //jwt key

        $website = SysHelper::getEnv('webhost');
        if (empty($website)) {
            $this->failure('website_not_exists');
        }
        $this->website = $website;
    }

    /**
     * 设置token
     * @param $userInfo
     */
    protected function setToken($userInfo)
    {
        $this->userInfo = $userInfo;
        $tokenData = $this->_buildTokenParam();
        $token = JWT::encode($tokenData, $this->jwt['key']);
        Session::set("token", $token);
    }

    /**
     * @return array
     */
    private function _buildTokenParam()
    {
        $iat = time();
        if (isset($this->ext['exp']) && $this->ext['exp'] < time()) {       //校验超时
            $this->ext = [];
        }
        $expTime = 30 * 24 * 60 * 60;      //其他默认30天
        return array(
            "iss" => $this->website,      //token发布人
            "iat" => $iat,      //token发布时间
            "exp" => time() + $expTime,         //超时时间
            "userInfo" => $this->userInfo,
            "ext" => $this->ext         //扩展信息
        );
    }

    /**
     * bool值为true验证是否为管理员
     * @return bool
     */
    protected function isLogin()
    {
        $isLogin = !empty($this->userInfo['user_id']);
        if (!$isLogin) {
            $this->redirect('/admin/login');
        }
        return $isLogin;
    }

    /**
     * @return bool
     */
    protected function isAdmin(){
        $isLogin = !empty($this->userInfo['admin']);
        if (!$isLogin) {
            $this->redirect('/admin/login');
        }
        return $isLogin;
    }

    public function validateModel($data, $validate) {
        // LogHelper::logSpider(get_called_class()." validate:".json_encode($data));
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }
    }
}