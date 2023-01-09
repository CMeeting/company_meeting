<?php


namespace App\Services;


use Carbon\Carbon;

class CommonService
{
    public static function formatDate($date){
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $date_obj = Carbon::parse($date);
        $month  = $date_obj->month;
        $month_index = $month-1;
        $month_en = $months[$month_index];
        $format = $date_obj->format('d, Y, H:i');

        return $month_en . ' ' . $format;
    }
}