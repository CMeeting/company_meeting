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
use App\Services\DocumentationService;
use App\Repositories\RolesRepository;
use App\Http\Requests\Admin\AdminLoginRequest;

class DocumentationController extends BaseController {



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
    public function createPlatformVersion(Request $request)
    {
        $param = $request->input();
        $documentation = new DocumentationService();
        $pid = (isset($param['pid']) && $param['pid']) ? $param['pid'] : 0;
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
                return error("平台/版本号名称/seotitle/H1title在相同分类下已存在");
            } else {
                if ($bool) {
                    if (isset($param['delid']) && $param['delid']) {
                        return success("操作成功");
                    }
                    return redirect('/admin/documentation/platformVersion');
                } else {
                    return error("操作失败");
                }
            }
        }
    }

    public function delPlatformVersion()
    {
        $param = $this->getRequstParam();
        $documentation = new DocumentationService();
        $bool = $documentation->addEditcaregorical($param);
        if ($bool) {
            return success("删除成功");
        } else {
            return failure("删除失败");
        }
    }

    public function showHideclassification()
    {
        $param = $this->getRequstParam();
        $documentation = new DocumentationService();
        if (isset($param['id'])) {
            $data = $documentation->showHide($param);
            if ($data) {
                return success("修改成功");
            } else {
                return failure("修改失败");
            }
        } else {
            return failure("缺少参数");
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
        View::assign('data', $data);
        //View::assign('childCateList', $data['childCateList']);
        return view();
    }


    public function createSdkClassification()
    {
        $param = $this->getRequstParam();
        $documentation = new DocumentationService();
        $sdkclassification = new SdkclassificationService();
        if (isset($param['id']) && $param['id']) {
            $data = $sdkclassification->getFindcategorical($param['id']);
        } else {
            $data['platformid'] = (isset($param['platformid']) && $param['platformid']) ? $param['platformid'] : 0;;
            $data['version'] = (isset($param['version']) && $param['version']) ? $param['version'] : 0;;
            $data['pid'] = (isset($param['pid']) && $param['pid']) ? $param['pid'] : 0;
            $pid = (isset($param['pid']) && $param['pid']) ? $param['pid'] : 0;
        }
        $fenlei = $documentation->getCategoricalData();
        View::assign('parent', $fenlei['parent']);
        View::assign('children', $fenlei['childCateList']);

        $categorical_data = $sdkclassification->getCategorical();
        View::assign('pid', isset($pid) ? $pid : 0);
        View::assign('data', $data);
        View::assign('material', $categorical_data);
        return view();
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


    public function sdkDocumentation()
    {
        $param = $this->getRequstParam();
        $sdksrvice = new SdKArticleService();
        $data = $sdksrvice->sele_list($param);
        $assign_where = CommonService::buildAssignWhere($param);
        View::assign('platformid', $sdksrvice->getplatform());
        View::assign('version', $sdksrvice->getversion());
        View::assign('classification_ids', $sdksrvice->getCategorical());
        View::assign('assign_where', $assign_where);
        View::assign('data', $data['data']['data']);
        View::assign('page', $data['page']);
        return view();
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
