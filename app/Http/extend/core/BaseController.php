<?php
/**
 * Created by PhpStorm.
 * User: LZZ
 * Date: 2019/4/16
 * Time: 14:03
 */

namespace core;


use app\api\model\Product;
use app\api\model\App;
use core\helper\JsonHelper;
use core\helper\LogHelper;
use core\helper\SysHelper;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use app\api\service\DevicesService;
use think\Controller;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;


class BaseController extends Controller
{

    /**
     * @var
     */
    private $param;
    /**
     * 请求头参数
     * @var
     */
    private $header;

    private $header_data;

    protected $app = [];

    protected $product = [];

    public $ext;

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
     * BaseController constructor.
     * @param App|null $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function __construct(\think\App $request = null)
    {
        parent::__construct($request);

        $this->header = Request::instance()->header();

        $data = file_get_contents("php://input");
        $this->param = JsonHelper::decode($data)['data'] ?? JsonHelper::decode($data);
        $str = strtolower(json_encode($this->param));
        $blockArr = ['select','insert','delete','update','where',' set ',' and ',' or '];
        $warnArr = [' set',' and',' or'];
        $ip = DevicesService::getClientIp();
        foreach ($warnArr as $item) {
            if (strpos($str,$item)) {
                LogHelper::logParam( $ip. ' ' . $item .' warn injectSQL:'.json_encode($this->param));
            }
        }
        foreach ($blockArr as $item) {
            if (strpos($str,$item)) {
                RedisModel::redis()->sadd('block_ips', $ip);
                if (RedisModel::redis()->ttl($ip) < 0) {
                    RedisModel::redis()->expire($ip, 864000);
                }
                LogHelper::logParam($ip. ' ' . $item .' error injectSQL:'.json_encode($this->param));
                LogHelper::logParam($ip. ' Request Url:' . $_SERVER['REQUEST_URI']);
                error('','');
            }
        }
        $this->header_data = JsonHelper::decode($data)['header'] ?? '';
        $this->_initConf();
    }

    public function validateModel($data, $validate) {
        // LogHelper::logSpider(get_called_class()." validate:".json_encode($data));
        if (!$validate->check($data)) {
            $this->failure('model', $validate->getError());
        }
    }

    /**
     * @param $app_code
     * @param null $product_code
     */
    public function validAppAndProduct($app_code, $product_code = null) {
        if (empty($product_code)) {
            $this->app = App::find(['code' => $app_code]);
            if (!empty($this->app)) {
                $this->product = Product::find(["app_id" => $this->app["id"]], '', 'code DESC');
            }
            if (empty($this->product)) {
                $this->failure("product_code", "invalid_product_code");
            }
            Log::write("AppCode mode: ".$this->product['code']);
        } else {
            $this->product = Product::find(["code" => $product_code]);
            if (empty($this->product)) {
                $this->failure("product_code", "invalid_product_code");
            } else {
                $this->app = App::find(['id' => $this->product["app_id"]]);
            }
            Log::write("ProductCode mode: ".$product_code);
        }

        if (empty($this->app)) {
            $this->failure("app_code", "invalid_app_code");
        }
    }


    /**
     * 失败返回
     * @param string $detailKey
     * @param null $data
     */
    protected function failure($attribute, $detailkey, $httpCode = 400)
    {
        $this->_response(null, $detailkey, $attribute, $httpCode);
    }

    /**
     * 成功返回(重载的tp5的success方法，$data后的参数是不要的)
     * @author  jiang
     * @param $data
     * @param string $msg
     * $msg = '', $url = null, $data = null, $wait = 3, array $header = []
     */
    protected function success($data = null, $msg = "", $url = '', $w = 0, array $header = [])
    {
        if (empty($data)) {
            $data = null;
        }
        $this->_response($data, $msg);
    }


    /**
     * @param $data
     * @param string $message
     * @param string $code
     */
    private function _response($data, $msg = '', $attribute = '', $code = 200)
    {
        $params = $this->getRequstParam();
        if (empty($params)) $params = Request::param();
        logApiDebugInfo($attribute, $msg, $code, $params);
        // $data = JsonHelper::format($data);
        header("Content-Type: application/json");   //类型
        header('Status:'.$code);
        $result['data'] = $data;
        if (!empty($msg)) {
            $result['errors'][] = [
                'attribute' => $attribute,
                'detail_key' => $msg,
            ];
        }
        echo JsonHelper::encode($result);
        exit;
    }


    /**
     * 获取请求参数
     * @param array $data
     * @return mixed
     */
    protected function getRequstParam(array $data = [])
    {
        foreach ($data as $value) {
            $bool = array_key_exists($value, $this->param);
            if (false === $bool) {
                if (Config::get('app.app_debug')) {
                    $this->failure($value, "参数" . $value . "有误");
                } else {
                    $this->failure($value, "参数有误");
                }
            }
        }
        if (isset($this->param['email'])) {
            $email = strtolower(strstr($this->param['email'], "@"));
            $this->param['email'] = str_replace(strstr($this->param['email'], "@"), $email, $this->param['email']);
        }
        if (isset($this->param['subscription']['email'])) {
            $email = strtolower(strstr($this->param['subscription']['email'], "@"));
            $this->param['email'] = str_replace(strstr($this->param['subscription']['email'], "@"), $email, $this->param['subscription']['email']);
        }
      //  if(NULL === $this->param) $this->param = Request::param();
        return $this->param;
    }


    /**
     * 初始化jwt函数
     */
    private function _initConf()
    {
        $jwt = SysHelper::getEnv('jwt');
        if (empty($jwt)) {
            $this->failure('jwt_not_exists', '');
        }
        if (!isset($jwt['key'])) {
            $this->failure('jwt_key_not_exists', '');
        }
        $this->jwt = $jwt;            //jwt key
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getUserInfo()
    {
        $token = $this->header_data['token'] ?? '';
        if (empty($token)) {
          error('not login','not_login');
        }
        $token = JWT::decode($token, $this->jwt['key'], ['HS256']);
        $this->userInfo = JsonHelper::decode(JsonHelper::encode($token))['userInfo'];
        return $this->userInfo;
    }

    protected function setToken($userInfo)
    {
        $this->userInfo = $userInfo;
        $tokenData = $this->_buildTokenParam();
        return JWT::encode($tokenData, $this->jwt['key']);
    }

    private function _buildTokenParam()
    {
        $iat = time();
        if (isset($this->ext['exp']) && $this->ext['exp'] < time()) {       //校验超时
            $this->ext = [];
        }
        $expTime = 30 * 24 * 60 * 60;      //其他默认30天
        return [
            "iss" => SysHelper::getEnv('webhost'),      //token发布人
            "iat" => $iat,      //token发布时间
            "exp" => time() + $expTime,         //超时时间
            "userInfo" => $this->userInfo,
            "ext" => $this->ext         //扩展信息
        ];
    }
}