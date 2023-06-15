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

    public function sdkindex()
    {
        $sdkclassification = new GoodsclassificationService();
        $data = $sdkclassification->getsdkCategoricalData();
        return $this->view('sdkindex',['data'=>$data]);
    }


    public function creategoodsClassification($pid=0)
    {
        $GoodsclassificationService = new GoodsclassificationService();
        $data['pid'] = $pid;
        $categorical_data = $GoodsclassificationService->getCategorical();
        return $this->view('createsdkclassification',['pid'=>$pid,'data'=>$data,"material"=>$categorical_data]);
    }

    public function createsaasgoodsClassification($pid=0){
        $GoodsclassificationService = new GoodsclassificationService();
        $data['pid'] = $pid;
        $categorical_data = $GoodsclassificationService->getSaaSCombo();
        return $this->view('createsaasclassification',['pid'=>$pid,'data'=>$data,"material"=>$categorical_data]);
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

    public function createRunsaasgoodsclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new GoodsclassificationService();
        if (!empty($param)) {
            $bool = $sdkclassification->addsaasEditcaregorical($param);
            if ($bool && $bool === "repeat") {
                flash('分类名称在相同分类下已存在')->error()->important();
                return redirect()->route('goodsclassification.sdkindex');
            } else {
                if ($bool) {
                    flash('添加成功')->success()->important();
                    return redirect()->route('goodsclassification.sdkindex');
                } else {
                    flash('添加失败')->error()->important();
                    return redirect()->route('goodsclassification.sdkindex');
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
    public function updatesaasgoodsClassification($id)
    {
        $sdkclassification = new GoodsclassificationService();
        $data = $sdkclassification->getFindcategorical($id);
        $categorical_data = $sdkclassification->getsaasCategorical();
        return $this->view('updatesaasgoodsclassification',['data'=>$data,"material"=>$categorical_data]);
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

    public function updatesaasRungoodsclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new GoodsclassificationService();
        if (!empty($param)) {
            $bool = $sdkclassification->addsaasEditcaregorical($param);
            if ($bool == "repeat") {
                flash('分类名称在相同分类下已存在')->error()->important();
                return redirect()->route('goodsclassification.sdkindex');
            } else {
                if ($bool==1) {
                    flash('修改成功')->success()->important();
                }elseif ($bool==0){
                    flash('暂无更新')->error()->important();
                } else {
                    flash('修改失败')->error()->important();
                }
                return redirect()->route('goodsclassification.sdkindex');
            }
        }
    }

    public function delgoodsclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new GoodsclassificationService();
        $bool = $sdkclassification->addEditcaregorical($param);
        if ($bool=="isdata") {
            return ['code'=>1,'msg'=>"该分类或子分类下有商品存在，不允许删除！"];
        }elseif($bool){
            return ['code'=>0];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }

    public function delsaasgoodsclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new GoodsclassificationService();
        $bool = $sdkclassification->addsaasEditcaregorical($param);
        if ($bool=="isdata") {
            return ['code'=>1,'msg'=>"该分类或子分类下有商品存在，不允许删除！"];
        }elseif($bool){
            return ['code'=>0];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }




}