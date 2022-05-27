@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>管理员管理</h5>
        </div>

        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="{{route('admins.create')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加管理员</button></a>
            <div class="col-xs-10 col-sm-5 margintop5">
                <form name="admin_list_sea" class="form-search" method="post" action="{:url('admin/Admin/admin_list')}">
                    <div class="input-group">
										<span class="input-group-addon">
											<i class="ace-icon fa fa-check"></i>
										</span>
                        <input type="text" name="search_name" class="form-control" value="" placeholder="输入需查询的用户名" />
                        <span class="input-group-btn">
											<button type="submit" class="btn btn-purple btn-sm">
												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
												搜索
											</button>
										</span>
                    </div>
                </form>
            </div>
            <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12 but-height">
                <div class="form-group">

                    <button type="button"  id="modal_excel" class="form-control btn blue" data-toggle="modal" data-target="#ListStyle" data-placement="top" placeholder="Chee Kin" >
                        <i class="fa fa-download "></i> 导出
                    </button>
                </div>
            </div>
            <form method="post" action="{{route('admins.index')}}" name="form">

                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th>用户名</th>
                        <th>用户权限</th>
                        <th class="text-center">最后登录IP</th>
                        <th class="text-center" width="150">最后登录时间</th>
                        <th class="text-center" width="150">注册时间</th>
                        <th class="text-center" width="150">注册IP</th>
                        <th class="text-center" width="80">登录次数</th>
                        <th class="text-center" width="80">状态</th>
                        <th class="text-center" width="200">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $k => $item)
                        <tr>
                            <td class="text-center">{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>
                                @foreach($item->roles as $role)
                                    {{$role->name}}
                                @endforeach
                            </td>
                            <td class="text-center">{{$item->last_login_ip}}</td>
                            <td class="text-center">{{$item->created_at->diffForHumans()}}</td>
                            <td class="text-center">{{$item->created_at->diffForHumans()}}</td>
                            <td class="text-center">{{$item->create_ip}}</td>
                            <td class="text-center">{{$item->login_count}}</td>
                            <td class="text-center">
                                @if($item->status == 1)
                                    <span class="text-navy">正常</span>
                                @elseif($item->status == 2)
                                    <span class="text-danger">锁定</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{route('admins.edit',$item->id)}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                    </a>
                                    @if($item->status == 2)
                                            <a href="{{route('admins.status',['status'=>1,'id'=>$item->id])}}"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 恢复</button></a>
                                    @else
                                            <a href="{{route('admins.status',['status'=>2,'id'=>$item->id])}}"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 禁用</button></a>
                                    @endif
                                    <a href="{{route('admins.delete',$item->id)}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$admins->links()}}
            </form>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<!-- 显示导出excel模态框（Modal） -->
<div class="modal fade  bs-example-modal-lg" id="ListStyle" aria-hidden="true"
     tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;z-index: 999">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">导出订单</h4>
            </div>



            <div class="panel-body">


                <div class="form-group">
                    <div class="col-xs-12   col-lg-12">
                        <!-- 								<div id="progress_order" class="progress pos-rel" data-percent="0%" > -->
                        <!-- 									<div  id="progress_orderbar"  class="progress-bar" style="width:0%;"></div> -->
                        <!-- 								</div> -->
                        <div id="progress_order" class="progress progress-striped" style="margin-top:1%;">
                            <div id="progress_orderbar" class="progress-bar progress-bar-info active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                <span>0%</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-xs-12   col-lg-12">
                        <button type="button" id="kaishidaochuanniu" class="btn btn-sm purple dropdown-toggle mt-ladda-btn ladda-button " data-style="zoom-in" onclick="return startdaochu();return false;">
                            <i class="fa fa-download"></i>
                            <font >开始导出</font>
                        </button>
                        <a>
                            <button type="button" id="kaishixiazaianniu" class="btn btn-sm purple disabled dropdown-toggle mt-ladda-btn ladda-button  "  onclick="" data-style="zoom-in">
                                <i class="fa fa-download"></i>
                                <font >下载文件</font>
                            </button>
                        </a>
                    </div>
                </div>

            </div>

            <div style="position:relative">
                <div class="panel-body table-responsive" id="searchTable">

                </div>
            </div>

            <div class="modal-footer"></div>

        </div>
    </div>
</div>
<!--END 显示导出excel模态框（Modal） -->
@endsection