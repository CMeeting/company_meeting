@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>角色管理</h5>
        </div>
        <div class="ibox-content">
{{--            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a> &nbsp;--}}
            <a href="{{route('roles.create')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加角色</button></a>
            <table class="table table-striped table-bordered table-hover m-t-md">
                <thead>
                <tr>
                    <th class="text-center" width="100">ID</th>
                    <th>角色名称</th>
                    <th>角色描述</th>
                    <th class="text-center" width="100">排序</th>
{{--                    <th class="text-center" width="100">状态</th>--}}
                    <th class="text-center" width="300">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $key => $item)
                    <tr>
                        <td  class="text-center" >{{$item->id}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->remark}}</td>
                        <td class="text-center">{{$item->order}}</td>
{{--                        <td class="text-center">--}}
{{--                            @if($item->status == 1)--}}
{{--                                <span class="text-navy">启用</span>--}}
{{--                            @else--}}
{{--                                <span class="text-danger">禁用</span>--}}
{{--                            @endif--}}
{{--                        </td>--}}
                        <td class="text-center">
                            <div class="btn-group">
                                @if($item->id ==1)
                                @else
                                    <a href="{{route('roles.access',$item->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 权限设置</button></a>
                                    <a href="{{route('roles.edit',$item->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                    <a onclick="del('{{$item->id}}')"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button>
{{--                                    <form class="form-common" action="{{ route('roles.destroy', $item->id) }}" method="post">--}}
{{--                                        {{ csrf_field() }}--}}
{{--                                        {{ method_field('DELETE') }}--}}
{{--                                    <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>--}}
{{--                                    </form>--}}
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$roles->links()}}
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<script>
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消']
        }, function(){
            // layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('roles.delete')}}",
                data: {id: id},
                type: 'get',
                // dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    //成功提示
                    if (resp.code==0) {
                        layer.msg("删除成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            window.location.reload()
                        });
                    } else {
                        //失败提示
                        if(resp.msg){
                            layer.msg(resp.msg, {
                                icon: 2,
                                time: 2000
                            });
                        }else {
                            layer.msg("请检查网络或权限设置！！！", {
                                icon: 2,
                                time: 2000
                            });
                        }
                    }
                }
            });
        }, function(index){
            layer.close(index);
        });
    }
</script>
@endsection