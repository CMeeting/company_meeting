<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogTagRequest;
use Illuminate\Http\Request;
use App\Services\AdminsService;
use App\Services\DocumentationService;
use App\Services\SdkclassificationService;
use App\Services\SdKArticleService;
use App\Repositories\RolesRepository;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentationController extends BaseController {


    /**
     * 平台/版本主页
     * @return mixed
     */
    public function platformVersion()
    {
        $documentation = new DocumentationService();
        $data = $documentation->getCategoricalData();
        return $this->view('platformversion',['cateList'=>$data['cateList'],'childCateList'=>$data['childCateList']]);
    }

    /**
     * 创建
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function createPlatformVersion($pid=0)
    {
        $documentation = new DocumentationService();
        $categorical_data = $documentation->getCategorical();
        return $this->view('createplatformversion',['pid'=>$pid,'material'=>$categorical_data]);
    }

    /**
     * 数据更新
     * @return array|string[]|\think\response\Redirect|\unknown[]|void
     * @throws \think\db\exception\DbException
     */
    public function createRunPlatformVersion(Request $request)
    {
        $param = $request->input();
        $documentation = new DocumentationService();
        if (!empty($param)) {
                $bool = $documentation->addEditcaregorical($param);
            if ($bool == "repeat") {
                flash('平台/产品名称/seotitle/H1title在相同分类下已存在')->error()->important();
                return redirect()->route('documentation.createPlatformVersion');
            } else {
                if ($bool) {
                    flash('添加成功')->success()->important();
                    return redirect()->route('documentation.platformVersion');
                } else {
                    flash('操作失败')->error()->important();
                    return redirect()->route('documentation.createPlatformVersion');
                }
            }
        }
    }

    /**
     * 修改平台/版本
     * @param $id
     * @return mixed
     */
    public function updatePlatformVersion($id)
    {
        $documentation = new DocumentationService();
        if (isset($id) && $id) {
            $data = $documentation->getFindcategorical($id);
        }else{
            flash('缺少参数')->error()->important();
            return redirect()->route('documentation.platformVersion');
        }
        $categorical_data = $documentation->getCategorical();
        return $this->view('updateplatformversion',['data'=>$data,'material'=>$categorical_data]);
    }

    /**
     * 修改平台/版本操作
     * @param $id
     * @return mixed
     */
    public function updateRunPlatformVersion(Request $request)
    {
        $param = $request->input();
        $documentation = new DocumentationService();
        if (!empty($param)) {
            $bool = $documentation->addEditcaregorical($param);
            if ($bool == "repeat") {
                flash('平台/产品名称/seotitle/H1title在相同分类下已存在')->error()->important();
                return redirect()->route('documentation.createPlatformVersion');
            } else {
                if ($bool==1) {
                    flash('修改成功')->success()->important();
                }elseif ($bool==0){
                    flash('暂无更新')->error()->important();
                } else {
                    flash('修改失败')->error()->important();
                }
                return redirect()->route('documentation.platformVersion');
            }
        }
    }

    /**
     * 删除
     */

    public function delPlatformVersion(Request $request)
    {
        $param = $request->input();
        $documentation = new DocumentationService();
        $bool = $documentation->addEditcaregorical($param);
        if ($bool) {
            return ['code'=>0];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }

    /**
     * 显示隐藏
     */
    public function showHideclassification(Request $request)
    {
        $param = $request->input();
        $documentation = new DocumentationService();
        if ($param['id']) {
            $data = $documentation->showHide($param);
            if ($data['code']) {
               return ['code'=>0,'status'=>$data['status']];
            } else {
               return ['code'=>1,'msg'=>"更新失败"];
            }
        } else {
            flash('缺少参数')->error()->important();
            return redirect()->route('documentation.platformVersion');
        }
    }

    /** SDK文章分类界面
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function sdkClassification()
    {
        $sdkclassification = new SdkclassificationService();
        $data = $sdkclassification->getCategoricalData();
        return $this->view('sdkclassification',['data'=>$data]);
    }


    public function createSdkClassification($pid=0,$platformid=0,$version=0)
    {
        $documentation = new DocumentationService();
        $sdkclassification = new SdkclassificationService();
        $data['platformid'] = $platformid;
        $data['version'] = $version;
        $data['pid'] = $pid;
        $fenlei = $documentation->getCategoricalData();
        $categorical_data = $sdkclassification->getCategorical();
        return $this->view('createsdkclassification',['pid'=>$pid,'data'=>$data,"material"=>$categorical_data,"parent"=>json_encode($fenlei['parent']),"children"=>$fenlei['childCateList']]);
    }


    public function createRunSdkclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new SdkclassificationService();
        if (!empty($param)) {
            $bool = $sdkclassification->addEditcaregorical($param);
            if ($bool == "repeat") {
                flash('分类名称在顶级/相同分类下已存在')->error()->important();
                return redirect()->route('documentation.createSdkClassification');
            } else {
                if ($bool) {
                    flash('添加成功')->success()->important();
                    return redirect()->route('documentation.sdkClassification');
                } else {
                    flash('添加失败')->error()->important();
                    return redirect()->route('documentation.createSdkClassification');
                }

            }
        }
    }

    public function updateSdkClassification($id)
    {
        $documentation = new DocumentationService();
        $sdkclassification = new SdkclassificationService();
        $data = $sdkclassification->getFindcategorical($id);
        $fenlei = $documentation->getCategoricalData();
        $categorical_data = $sdkclassification->getCategorical($data);
        return $this->view('updatesdkclassification',['data'=>$data,"material"=>$categorical_data,"parent"=>json_encode($fenlei['parent']),"children"=>$fenlei['childCateList']]);
    }
    public function updateRunSdkclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new SdkclassificationService();
        if (!empty($param)) {
            $bool = $sdkclassification->addEditcaregorical($param);
            if ($bool == "repeat") {
                flash('分类名称在顶级/相同分类下已存在')->error()->important();
                return redirect()->route('documentation.sdkClassification');
            } else {
                if ($bool==1) {
                    flash('修改成功')->success()->important();
                }elseif ($bool==0){
                    flash('暂无更新')->error()->important();
                } else {
                    flash('修改失败')->error()->important();
                }
                return redirect()->route('documentation.sdkClassification');
            }
        }
    }

    public function delSdkclassification(Request $request)
    {
        $param = $request->input();
        $sdkclassification = new SdkclassificationService();
        $bool = $sdkclassification->addEditcaregorical($param);
        if ($bool) {
            return ['code'=>0];
        } else {
            return ['code'=>1,'msg'=>"更新失败"];
        }
    }

    public function sdkDocumentation(Request $request)
    {
        $param = $request->input();
        $sdksrvice = new SdKArticleService();
        $data = $sdksrvice->sele_list($param);
        $platformid = $sdksrvice->getplatform();
        $version = $sdksrvice->getversion();
        $classification_ids = $sdksrvice->getCategorical();
        $query["query_type"] = isset($param['query_type']) ? $param['query_type'] : "";
        $query["info"] = isset($param['info']) ? $param['info'] : "";
        $query["platformid"] = isset($param['platformid']) ? $param['platformid'] : "";
        $query["version"] = isset($param['version']) ? $param['version'] : "";
        $query["classification_ids"] = isset($param['classification_ids']) ? $param['classification_ids'] : "";
        return $this->view('Sdkdocumentation',['data'=>$data,'query'=>$query,'platformid'=>$platformid,'version'=>$version,'classification_ids'=>$classification_ids]);
    }


    function page_with_array($request,$array_item){
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($array_item);
        $perPage = 10; // 每页数量
        $currentPageItems = $itemCollection->slice(($currentPage*$perPage)-$perPage,$perPage)->all();
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator($currentPageItems,count($itemCollection),$perPage);
        return $paginatedItems->setPath($request->url());

    }



    public function createsdkDocumentation($classification_ids=0)
    {
        $sdkclassification = new SdkclassificationService();
        $categorical_data = $sdkclassification->getCategorical();
        return $this->view('createsdkdocumentation',['classification_ids'=>$classification_ids,'material'=>$categorical_data]);
    }

    public function createRunsdkDocumentation(Request $request)
    {
        //$admin = $this->getUserInfo();
        $param = $request->input();
        $SdKArticleService = new SdKArticleService();
        if (!empty($param)) {
            if (empty($param['data']['classification_ids']) && !isset($param['delid'])){
                flash('请选择文章分类')->error()->important();
                return redirect()->route('documentation.createsdkDocumentation');
            }
            $bool = $SdKArticleService->addEditcaregorical($param);
            if ($bool['code'] == 1) {
                    flash('添加成功')->success()->important();
                    if(isset($param['type'])){
                        return redirect()->route('documentation.sdkClassification');
                    }
                    return redirect()->route('documentation.sdkDocumentation');
                } else {
                flash($bool['msg'])->error()->important();
                return redirect()->route('documentation.createsdkDocumentation');
            }
        }
    }

    public function updatesdkDocumentation($id)
    {
        $sdkclassification = new SdkclassificationService();
        $SdKArticleService = new SdKArticleService();
        $data = $SdKArticleService->getFindcategorical($id);
        $categorical_data = $sdkclassification->getCategorical();
        return $this->view('updatesdkdocumentation',['data'=>$data,'material'=>$categorical_data]);
    }

    public function updateRunsdkDocumentation(Request $request)
    {
        $param = $request->input();
        $SdKArticleService = new SdKArticleService();
        if (!empty($param)) {
            if (empty($param['data']['classification_ids']) && !isset($param['delid'])){
                flash('请选择文章分类')->error()->important();
                return redirect()->route('documentation.createsdkDocumentation');
            }
            $bool = $SdKArticleService->addEditcaregorical($param);
            if ($bool['code'] == 1) {
                flash('修改成功')->success()->important();
                return redirect()->route('documentation.sdkDocumentation');
            } else {
                flash($bool['msg'])->error()->important();
                return redirect()->route('documentation.sdkDocumentation');
            }
        }
    }
    public function delsdkDocumentation(Request $request)
    {
        $param = $request->input();
        $SdKArticleService = new SdKArticleService();
        if (!empty($param)) {
            $bool = $SdKArticleService->addEditcaregorical($param);
            if ($bool['code'] == 1) {
                return ['code'=>0];
            } else {
                return ['code'=>1,'msg'=>$bool['msg']];
            }
        }
    }


    public function update_leve(Request $request)
    {
        $param = $request->input();
        $SdKArticleService = new SdkclassificationService();
        if (!empty($param)) {
            $bool = $SdKArticleService->update_level($param['id']);
            if ($bool['code'] == 1) {
                return ['code'=>0];
            } else {
                return ['code'=>1,'msg'=>$bool['msg']];
            }
        }
    }
}
