<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2018/8/8
 * Time: 10:05
 */

namespace core\common;


class JWTUserParam
{
    public $user_id;
    public $avatar;
    public $nickname;
    public $sex;

    /**
     * JWTUserParam constructor.
     * @param $userId
     * @param $userName
     * @param $avatar
     * @param $nickname
     * @param $sex
     * @param $userType
     * @param $userLevel
     * @param $recode
     */
    public function __construct($userId = '', $avatar = '', $nickname = '', $sex = '')
    {
        $this->user_id = $userId;
        $this->avatar = $avatar;
        $this->nickname = $nickname;
        $this->sex = $sex;
    }

    /**
     * 获取实例
     * @param $user_id
     * @param $user_name
     * @param $avatar
     * @param $nickname
     * @param $sex
     * @param $userType
     * @param $userLevel
     * @param $recode
     * @return JWTUserParam
     * @throws BaseException
     */
    public static function getInstance($user_id, $avatar, $nickname, $sex)
    {
        if (empty($user_id) || empty($nickname)) {
            throw new BaseException('param_not_exists');
        }
        return new self($user_id, $avatar, $nickname, $sex);
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return string
     */
    public function getSex(){
        return $this->sex;
    }

    /**
     * @param $sex
     */
    public function setSex($sex){
        $this->sex = $sex;
    }

    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}