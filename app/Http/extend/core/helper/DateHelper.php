<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2019/11/1
 * Time: 15:55
 */

namespace core\helper;


use DateTime;
use DateTimeZone;
use think\Exception;

class DateHelper
{
    protected $dt;
    protected $ttz; //目标时区
    protected static $instance;

    private function __construct($ftz, $ttz)
    {
        if (!$ftz || !$ftz instanceof DateTimeZone)
            $ftz = new DateTimeZone(date_default_timezone_get()); //默认来源时区

        if (!$ttz || !$ttz instanceof DateTimeZone)
            $this->ttz = new DateTimeZone(C('PARTNER_TIME_ZONE')); //默认输出时区
        else
            $this->ttz = $ttz;

        $this->dt = new DateTime('now', $ftz);
    }

    /**
     * date 2019/1/11 15:19
     * @param null $ftz 来源时区
     * @param null $ttz 目标时区
     */
    public static function getInstance($ftz = 'UTC', $ttz = 'Etc/GMT-8')
    {
        $ftz = new \DateTimeZone($ftz);
        $ttz = new \DateTimeZone($ttz);
        if (!$ftz && !$ttz)
            $key = '0';
        else {
            $ftz_key = $ftz instanceof DateTimeZone ? $ftz->getName() : null;
            $ttz_key = $ttz instanceof DateTimeZone ? $ttz->getName() : null;
            $key = md5($ftz_key . $ttz_key);
        }
        if (!self::$instance[$key] instanceof self) {
            self::$instance[$key] = new self($ftz, $ttz);
        }

        return self::$instance[$key];
    }

    public function show($original, $formart = 'Y-m-d H:i:s')
    {
        //将时间转成时间戳
        if (strlen($original) != 10) $original = strtotime($original);

        $datetime = null;
        if ($this->dt->getTimezone()->getName() == date_default_timezone_get()) {
            $datetime = clone $this->dt;
            $datetime = $datetime->setTimestamp($original);
        } else {
            $tmpDt = new DateTime();
            $tmpDt->setTimestamp($original);
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $tmpDt->format('Y-m-d H:i:s'), $datetime->getTimezone());
        }
        $datetime->setTimezone($this->ttz);
        return $datetime->format($formart);
    }


    public static function periodDate($start_time, $end_time = '', $time = 'day', $hour = false)
    {
        if($start_time > $end_time){
            return [];
        }
        $hour = $hour ? ' H:i' : '';
        if (empty($end_time)) {
            $end_time = date('Y-m-d');
        }
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time) - 1;
        $i = 0;
        while ($start_time <= $end_time) {
            $arr[$i] = date('Y-m-d' . $hour . '', $start_time);
            $start_time = strtotime('+1 ' . $time . '', $start_time);
            $i++;
        }

        return $arr;
    }
}