<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class IndexsController extends Controller
{

    public function index()
    {
        return view('admin.indexs.index');
    }

    public function main()
    {
        Carbon::setLocale('fr');// fr time
        return view('admin.indexs.main')->with(['now' => Carbon::now() . " | " .Carbon::now()->formatLocalized('%A')]);;
    }
}
