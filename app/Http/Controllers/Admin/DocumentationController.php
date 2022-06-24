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
        return $this->view('createplatformVersion',['pid'=>$pid,'material'=>$categorical_data]);
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
                flash('平台/版本号名称/seotitle/H1title在相同分类下已存在')->error()->important();
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
                flash('平台/版本号名称/seotitle/H1title在相同分类下已存在')->error()->important();
                return redirect()->route('documentation.createPlatformVersion');
            } else {
                if ($bool==1) {
                    flash('修改成功')->success()->important();
                    return redirect()->route('documentation.platformVersion');
                }elseif ($bool==0){
                    flash('暂无更新')->success()->important();
                    return redirect()->route('documentation.platformVersion');
                } else {
                    flash('修改失败')->error()->important();
                    return redirect()->route('documentation.updatePlatformVersion');
                }
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
            if ($data) {
               return ['code'=>0];
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
        return $this->view('createsdkclassification',['pid'=>$pid,'data'=>$data,"material"=>$categorical_data,"parent"=>$fenlei['parent'],"children"=>$fenlei['childCateList']]);
    }


    public function createRunSdkclassification()
    {
        $param = $this->getRequstParam();
        $sdkclassification = new SdkclassificationService();
        if (!empty($param)) {
            $bool = $sdkclassification->addEditcaregorical($param);
            if ($bool == "repeat") {
                return error("分类名称在顶级/相同分类下已存在");
            } else {
                if ($bool) {
                    if (isset($param['delid'])) {
                        return success("操作成功");
                    }
                    return redirect('/admin/documentation/sdkClassification');
                } else {
                    return failure("操作失败");
                }
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
        return $this->view('sdkdocumentation',['data'=>$data['data']]);
    }

    public function createsdkDocumentation()
    {
        $DraftBox = new DraftBox();
        $sdkclassification = new SdkclassificationService();
        $SdKArticleService = new SdKArticleService();
        $admin = $this->getUserInfo();
        $param = $this->getRequstParam();
        $where['admin_id'] = $admin['user_id'];
        $where['type'] = 'SdkDocumentation';
        if (isset($param['id']) && $param['id']) {
            $data = $SdKArticleService->getFindcategorical($param['id']);
        }
        if (!isset($param['delid'])) {
            $page = isset($param['page']) ? $param['page'] : 1;
            if(isset($param['platformid'])){
                $assign_where = "platformid={$param['platformid']}&version={$param['version']}&classification={$param['classification']}&created_at_start={$param['created_at_start']}&created_at_end={$param['created_at_end']}&updated_at_start={$param['updated_at_start']}&updated_at_end={$param['updated_at_end']}&commit={$param['commit']}&page=$page";
            }else{
                $assign_where = "page=$page";
            }
        }
        $categorical_data = $sdkclassification->getCategorical();
        $draft = $DraftBox->find_draft($where);
        $draft = $draft ? json_decode($draft, true) : "";
        View::assign('draft', $draft);
        View::assign('assign_where', $assign_where);
        View::assign('classification_ids', isset($param['classification_ids']) ? $param['classification_ids'] : 0);
        View::assign('data', $data ?? []);
        View::assign('material', $categorical_data);
        return view();
    }

    public function createRunsdkDocumentation()
    {
        $admin = $this->getUserInfo();
        $param = $this->getRequstParam();
        $SdKArticleService = new SdKArticleService();
        if (!empty($param)) {
            if (empty($param['data']['classification_ids']) && !isset($param['delid'])) return error("请选择文章分类");
            $bool = $SdKArticleService->addEditcaregorical($param, $admin['user_id']);
            if ($bool['code'] == 1) {
                if (isset($param['delid'])) {
                    return success("操作成功");
                } elseif (isset($param['data']['id'])) {
                    return redirect('/admin/documentation/sdkDocumentation?' . $param['assign_where']);
                } else {
                    return redirect('/admin/documentation/sdkDocumentation');
                }
            } else {
                return error($bool['msg']);
            }
        }
    }
}
