<?php


namespace App\Services;


use App\Models\User;

/**
 * 存
 * Class JWTService
 * @package App\Services
 */

class JWTService
{
    //头部信息
    private static $header = [
        'alg' => 'sha256',
        'type' => 'JWT'
    ];

    //jwt秘钥
    private static $jwt_key = '123456';

    //token加密iv
    private static $iv = 'abcdefgh';

    //token加密方式
    private static $method = 'DES-EDE-CFB';

    //token加密key
    private static $token_key = '123456';

    /**
     * payload 格式 ['email'=>'test@gmail.com', 'iat'=>'签发时间', 'jti'=>'token唯一标识']
     * 生成token
     * @param $payload
     * @return string
     */
    public static function getToken($payload){
        $base64header = self::base64UrlEncode(json_encode(self::$header));
        $base64payload = self::base64UrlEncode(json_encode($payload));
        $signature = self::signature($base64header . '.' . $base64payload);

        return $base64header . '.' . $base64payload . '.' . $signature;
    }

    /**
     * base64 编码 +/ 会变成 %**的格式，造成数据库存储问题，替换掉
     * @param $input
     * @return string|string[]
     */
    private static function base64UrlEncode($input){
        //替换=号，base64会在末尾填充=号
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * base64 解码
     * @param $input
     * @return false|string
     */
    private static function base64UrlDecode($input){
        //补全替换掉的=号
        $remainder = strlen($input) % 4;
        if($remainder){
            $add_len = 4 - $remainder;
            $input .= str_repeat('=', $add_len);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * 生成签名
     * @param $input
     * @return string|string[]
     */
    private static function signature($input){
        $signature = hash_hmac(self::$header['alg'], $input, self::$jwt_key);
        return self::base64UrlEncode($signature);
    }

    /**
     * 加密token
     * @param $token
     * @return false|string
     */
    public static function encryptToken($token){
        $str = User::getRandStr(5);
        return openssl_encrypt($token . $str, self::$method, self::$token_key, OPENSSL_ZERO_PADDING, self::$iv);
    }

    /**
     * 解密token
     * @param $str
     * @return false|string
     */
    public static function decryptToken($str){
        $result = openssl_decrypt($str, self::$method, self::$token_key, OPENSSL_ZERO_PADDING, self::$iv);
        //去掉拼接的字符串
        return substr($result, 0, -5);
    }

    /**
     * 验证token
     * @param $token
     * @return array
     */
    public static function verifyToken($token){
        $tokens = explode('.', $token);
        if(count($tokens) != 3){
            return ['code'=>401, 'msg'=>'Invalid Token'];
        }

        list($base64header, $base64payload, $sign) = $tokens;

        //签名验证
        $current_sign = self::signature($base64header . '.' . $base64payload);
        if($current_sign !== $sign){
            return ['code'=>401, 'msg'=>'Invalid Token'];
        }

        //签发时间
        $payload = json_decode(self::base64UrlDecode($base64payload));
        if(!isset($payload->iat) || $payload->iat > time()){
            return ['code'=>401, 'msg'=>'Invalid Token'];
        }

        //是否过期
        if(!isset($payload->jti)){
            return ['code'=>401, 'msg'=>'Expired Token'];
        }elseif(!\Cache::has('jwt' . $payload->email)){
            return ['code'=>401, 'msg'=>'Expired Token'];
        }elseif(\Cache::get('jwt' . $payload->email) != $payload->jti){
            return ['code'=>401, 'msg'=>'Expired Token'];
        }

        return ['code'=>200, 'payload'=>$payload];
    }

    /**
     * 获取载荷
     * @param $token
     * @return mixed
     */
    public function getPayLoad($token){
        $tokens = explode('.', $token);
        return $tokens[1];
    }

    /**
     * 获取JWT唯一标识
     * @return string
     */
    public static function getJTI(){
        return md5(uniqid('jwt') . time());
    }

    /**
     * 删除token
     * @param $email
     */
    public static function forgetToken($email){
        $key = 'jwt' . $email;
        if(\Cache::has($key)){
            \Cache::forget($key);
        }
    }

    /**
     * 缓存token
     * @param $email
     * @param $jti
     */
    public static function saveToken($email, $jti)
    {
        $key = 'jwt' . $email;
        self::forgetToken($email);
        \Cache::add($key, $jti, 60 * 24 * 14);
    }
}