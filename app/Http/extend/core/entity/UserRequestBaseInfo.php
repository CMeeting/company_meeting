<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2018/12/20
 * Time: 15:35
 */

namespace core\entity;


class UserRequestBaseInfo
{
    /**
     * @var int 用户ID
     */
    private $userId;
    /**
     * @var int 平台id
     */
    private $platform;


    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }


    /**
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param mixed $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }


    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}