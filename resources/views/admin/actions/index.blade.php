@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>管理员操作日志</h5>
        </div>
        <div class="ibox-content">

                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('actions.index')}}">
                        <div class="input-group">

                            <div class="input-group-btn" style="display: block;width: 130px;float: left">
                                <select id="query_type" name="query_type" class="form-control"
                                        style="display: inline-block;width: 130px;float: left;">
                                    <option value="orders.id" selected >
                                        管理员账号
                                    </option>
                                </select>
                            </div>
                            <input id="info" type="text" name="info" placeholder="请输入筛选内容" class="form-control" style="float: left;display: inline-block;width: 150px;
                                   value="@if(isset($query['info']))value="{{$query['info']}}"@endif"/>

                            <span class="input-group-btn" style="display: inline-block;float: left">
                                                    <button type="submit" class="btn btn-purple btn-sm"
                                                            style="margin-left: 20px;" id="selectd">
                                                        <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                                        搜索
                                                    </button>
                                                </span>
                        </div>
                    </form>
                </div>
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th class="text-center" width="150">用户名</th>
{{--                        <th class="text-center" width="150">拥有权限</th>--}}
                        <th class="text-center" >操作内容</th>
                        <th class="text-center" width="200">操作地址</th>
                        <th class="text-center" width="150">登录时间</th>
{{--                        <th class="text-center" width="100">操作</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($actions as $key => $item)
                        @if($item->type == 1)
                        <tr>
                            
                            <td class="text-center">{{$item->id}}</td>
                            <td class="text-center">{{$item->admin?$item->admin->name:""}}</td>
{{--                            <td class="text-center">--}}
{{--                                @foreach($item->admin->roles as $role)--}}
{{--                                    {{$role->name}}--}}
{{--                                @endforeach--}}
{{--                            </td>--}}
                            <td>{{$item->info}}</td>
                            <td class="text-center">{{$item->data['ip']}}<br>来自：{{$item->data['address']}}</td>
                            <td class="text-center">{{$item->created_at->diffForHumans()}}</td>
{{--                            <td class="text-center">--}}
{{--                                <form class="form-common" action="{{route('actions.destroy',$item->id)}}" method="post">--}}
{{--                                    {{ csrf_field() }}--}}
{{--                                    {{ method_field('DELETE') }}--}}
{{--                                    <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>--}}
{{--                                </form>--}}
{{--                            </td>--}}
                        </tr>
                        @else
                            <tr>
                                <td class="text-center">{{$item->id}}</td>
                                <td class="text-center">{{isset($item->admin_id) ? $item->admin->name : '暂无'}}</td>
                                <td class="text-center">
                                    @if($item->admin_id)
                                        @foreach($item->admin->roles as $role)
                                            {{$role->name}}
                                        @endforeach
                                    @else
                                        暂无
                                    @endif
                                </td>
                                <td>{{$item->data['action']}}</td>
                                <td class="text-center">{{$item->data['ip']}}<br>来自：{{$item->data['address']}}</td>
                                <td class="text-center">{{$item->created_at->diffForHumans()}}</td>
{{--                                <td class="text-center">--}}
{{--                                    <form class="form-common" action="{{route('actions.destroy',$item->id)}}" method="post">--}}
{{--                                        {{ csrf_field() }}--}}
{{--                                        {{ method_field('DELETE') }}--}}
{{--                                        <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>--}}
{{--                                    </form>--}}
{{--                                </td>--}}
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            <div class="pull-right pagination m-t-no">
                <div class="text-center">
                    {{$actions->appends(['query' => $query])->links()}}
                </div>
                <div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection