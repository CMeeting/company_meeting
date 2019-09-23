<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin\Count;
use App\Models\ConfigModel;
use App\Http\Controllers\Admin\BaseController;
use App\Http\Requests\Count\ConfigRequest;
/**
 * Description of ConfigController
 *
 * @author 七彩P1
 */
class ConfigController extends BaseController {
    //put your code here
    public  function index(ConfigRequest $request){
        $configModel = (new ConfigModel);
        
        $checkKey = $key = $request->get("key")?$request->get("key"):"pfid";
        $data = $configModel->where("key",$key)->paginate(20);
        $showKeyArr = $configModel->showKeyArr;
        return view("admin.config.index", compact('data','showKeyArr','checkKey'));
    }
    
    public  function create($key,ConfigRequest $request){
        $configModel = (new ConfigModel);
        
        $checkKey = $key ;
        $showKeyArr = $configModel->showKeyArr;
        if($request->get("id")){
            $item=$configModel->where("id",$request->get("id"))->first()->toArray();
        }else{
            $item ="";
        }
        return view("admin.config.create", compact('showKeyArr','checkKey','item'));
    }
    
    public  function opeary($id,ConfigRequest $request){
        $datas = $request->all();
        $configModel = (new ConfigModel);
        $showKeyArr = $configModel->showKeyArr;
        if($id){//修改
            $update["key"] = $datas["key"];
            $update["type"] = $datas["type"];
            $update["value"] = $datas["value"];
            $configModel->where("id",$id)->update($update);
            flash('更新'.$showKeyArr[$datas["key"]].'成功')->success()->important();
        }else{//添加
            $insert["key"] = $datas["key"];
            $insert["type"] = $datas["type"];
            $insert["value"] = $datas["value"];
            $configModel->insert($insert);
            flash('添加'.$showKeyArr[$datas["key"]].'成功')->success()->important();
        }
        return redirect()->route('config.index',["key"=>$datas["key"]]);
    }
    
    
    public function delete($id){
        $configModel = (new ConfigModel);
        $config = $configModel->where("id",$id)->first();

        if (empty($config)) {
            flash('删除失败')->error()->important();
            return redirect()->route('config.index');
        }
        $configModel->where("id",$id)->delete();
        flash('删除成功')->success()->important();
        return redirect()->route('config.index');
    }
}
