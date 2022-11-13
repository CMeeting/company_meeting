<?php
/**
 * 请求参数
 * Created by PhpStorm.
 * User: lzz
 * Date: 2018/6/15
 * Time: 15:28
 */
namespace core\common;

class HttpParam
{

    public $data;

    public $head;

    private function __construct(Head $head, $data)
    {
        $this->head = $head;
        $this->data = $data;
    }

    public static function build(Head $requestHead, $data)
    {
        return new self($requestHead, $data);
    }

    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}