@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <button class="btn btn-primary btn-sm" 
                    @if($level==0)
                    style="background-color:#5440ff;border-color:#5440ff" 
                    @else
                    style="background-color:#d5d4d4;border-color:#d5d4d4" 
                    @endif
                    type="button"><a href="{{route('admin.management')}}" link-url="javascript:void(0)" style="color:#fffcfc">运营</a></button>
            @foreach($levelArr as $key=>$name)
            @if($key !=-1)
            <button class="btn btn-primary btn-sm" 
                    @if($level==$key)
                    style="background-color:#5440ff;border-color:#5440ff" 
                    @else
                    style="background-color:#d5d4d4;border-color:#d5d4d4" 
                    @endif
                    type="button"><a href="{{route('admin.pfidIndex',["level"=>$key])}}" link-url="javascript:void(0)" style="color:#fffcfc">{{$name}}</a></button>
            @endif
            @endforeach        
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="{{route('admin.createPfid',["level"=>$level,"id"=>0])}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加管理员</button></a>
            <form method="post" action="{{route('admins.index')}}" name="form">
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th>{{$levelArr[$level]}}ID</th>
                        <th>{{$levelArr[$level]}}名</th>
                        
                        <th>备注</th>
                        <th>登录账号</th>
                        <th>登录密码</th>
                        <th class="text-center">下载二维码</th>
                        <th class="text-center" width="200">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $k => $item)
                        <tr>
                            <td class="text-center">
                                @if($level==1)
                                {{$item->pfid}}
                                @elseif($level==2)
                                {{$item->usid}}
                                @elseif($level==3)
                                {{$item->id}}
                                @endif
                            </td>
                            
                            <td>
                                 @if($level==1)
                                {{$pfidArr[$item->pfid]}}
                                @elseif($level==2)
                                {{$usidArr[$item->usid]}}
                                @elseif($level==3)
                                {{$item->id}}
                                @endif
                            </td>
                            <td>{{$item->remark}}</td>
                            
                            <td>{{$item->name}}</td>
                            <td class="text-center">{{$item->show_password}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{route('admin.createPfid',["level"=>$level,"id"=>$item->id])}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 编辑</button>
                                    </a>
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
@endsection