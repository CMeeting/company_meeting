<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\GoodsclassificationService;

class GoodsclassificationController extends BaseController {

    public function index()
    {
        $sdkclassification = new GoodsclassificationService();
        $data = $sdkclassification->getCategoricalData();
        return $this->view('index',['data'=>$data]);
    }


    public function creategoodsClassification($pid=0)
    {
        $GoodsclassificationService = new GoodsclassificationService();
        $data['pid'] = $pid;
        $categorical_data = $GoodsclassificationService->getCategorical();
        return $this->view('createsdkclassification',['pid'=>$pid,'data'=>$data,"material"=>$categorical_data]);
    }


    public function createRungoodsclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new GoodsclassificationService();
        if (!empty($param)) {
            $bool = $sdkclassification->addEditcaregorical($param);
            if ($bool && $bool === "repeat") {
                flash('分类名称在相同分类下已存在')->error()->important();
                return redirect()->route('goodsclassification.index');
            } else {
                if ($bool) {
                    flash('添加成功')->success()->important();
                    return redirect()->route('goodsclassification.index');
                } else {
                    flash('添加失败')->error()->important();
                    return redirect()->route('goodsclassification.index');
                }

            }
        }
    }

    public function updategoodsClassification($id)
    {
        $sdkclassification = new GoodsclassificationService();
        $data = $sdkclassification->getFindcategorical($id);
        $categorical_data = $sdkclassification->getCategorical();
        return $this->view('updategoodsclassification',['data'=>$data,"material"=>$categorical_data]);
    }
    public function updateRungoodsclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new GoodsclassificationService();
        if (!empty($param)) {
            $bool = $sdkclassification->addEditcaregorical($param);
            if ($bool == "repeat") {
                flash('分类名称在相同分类下已存在')->error()->important();
                return redirect()->route('goodsclassification.index');
            } else {
                if ($bool==1) {
                    flash('修改成功')->success()->important();
                }elseif ($bool==0){
                    flash('暂无更新')->error()->important();
                } else {
                    flash('修改失败')->error()->important();
                }
                return redirect()->route('goodsclassification.index');
            }
        }
    }

    public function delgoodsclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new GoodsclassificationService();
        $bool = $sdkclassification->addEditcaregorical($param);
        if ($bool) {
            return ['code'=>0];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }




}