<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminRequest;
use Illuminate\Http\Request;
use App\Services\AdminsService;
use App\Repositories\RolesRepository;
use App\Http\Requests\Admin\AdminLoginRequest;

class DocumentationController extends BaseController {
     public function __construct()
     {

     }


     public function index(){
         echo "测试页面";
     }
}
