@extends('admin.layouts.layout')
@section('content')
    <style>
        .abutton{
            display: inline-block;
            border-radius: 10px;
            border: 1px solid lavenderblush;
            margin-right: 3px;
            width: 75px;
            padding: 0px 5px 0px 5px;
            text-decoration:none;
            color: #f6fff8;
        }
        .clorosx{
            background-color: #0dbd2d;
        }
        .cloros{
            background-color: #20e281;
        }
        .cloros1{
            background-color: #e2322d;
        }
        .cloros2{
            background-color: #0b94ea;
        }
        .cloros3{
            background-color: #7f3fe2;
        }
        .cloros4{
            background-color: red;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Newsletter</h5>
                <a style="float: right" href="{{route('newsletter.createnewsletter')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> New Newsletter</button></a>
            </div>

            <div class="ibox-content">

                <form method="post" action="{{route('newsletter.newsletter_list')}}" name="form" style="width: 100%;overflow: auto;">

                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th class="text-center" width="50">ID</th>
                            <th>电子报模板名称</th>
                            <th>电子报标题</th>
                            <th>创建人</th>
                            <th class="text-center">创建时间</th>
                            <th class="text-center">编辑时间</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr id="han_{{$value->id}}">
                                <td class="text-center">{{$value->id}}</td>
                                <td style="word-break: break-word;">{{$value->name}}</td>
                                <td style="word-break: break-word;">{{$value->title}}</td>
                                <td class="text-center">{{$value->admin_name}}</td>
                                <td class="text-center">{{$value->created_at}}</td>
                                <td class="text-center">{{$value->updated_at}}</td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <span id="span_{{$value->id}}">
                                            @if($value->deleted==0)
                                                <a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="发送电子报" onclick="dd({{$value->id}})">
                                                <i class="fa fa-newspaper-o"></i> 发送
                                                </a>
                                            @endif
                                        </span>

                                        <a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="预览 " href="{{route('newsletter.newsletter_info',$value->id)}}">
                                            <i class="fa fa-users"></i> 预览
                                        </a>
                                        <a class="edit_3 abutton cloros3" style="text-decoration: none;color: #f6fff8" title="编辑 " href="{{route('newsletter.updatenewsletter',$value->id)}}">
                                            <i class="fa fa-edit"></i> 编辑
                                        </a>
                                        <span id="deleted_{{$value->id}}">
                                        @if($value->deleted==0)
                                        <a class="abutton cloros4" style="text-decoration: none;color: #f6fff8" title="当前启用状态" onclick="del('{{$value->id}}',{{$value->deleted}})">
                                            <i class="fa fa-trash-o fa-delete"></i>禁用
                                        </a>
                                        @else
                                        <a class="abutton clorosx" style="text-decoration: none;color: #f6fff8" title="当前禁用状态" onclick="del('{{$value->id}}',{{$value->deleted}})">
                                            <i class="fa fa-newspaper-o fa-delete"></i>启用
                                        </a>
                                        @endif
                                          </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $data->links() }}
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

@endsection
<script>
    function del(id,status){
        if(status==0){
            var title="您确定禁用吗？";
        }else{
            var title="您确定启用吗？";
        }
        layer.confirm(title, {
            btn: ['确定','取消']
        }, function(){
            layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('newsletter.delnewsletter')}}",
                data: {delid:id, _token: '{{ csrf_token() }}'},
                type: 'post',
                dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    //成功提示
                    if (resp.code==0) {
                        layer.msg("切换成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            if(resp.status==1){
                                $("#span_"+id).html("");
                                $("#deleted_"+id).html('<a class="abutton clorosx" style="text-decoration: none;color: #f6fff8" title="当前禁用状态" onclick="del('+id+','+resp.status+')"><i class="fa fa-newspaper-o fa-delete"></i>启用 </a>');
                            }else{
                                $("#span_"+id).html('<a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="发送电子报" onclick="dd('+id+')"><i class="fa fa-newspaper-o"></i> 发送 </a>');
                                $("#deleted_"+id).html('<a class="abutton cloros4" style="text-decoration: none;color: #f6fff8" title="当前启用状态" onclick="del('+id+','+resp.status+')"><i class="fa fa-trash-o fa-delete"></i>禁用</a>');
                            }
                        });

                    } else {
                        //失败提示
                        layer.msg(resp.message, {
                            icon: 2,
                            time: 2000
                        });
                    }
                },error:function(response){
                    layer.msg("请检查网络或权限设置！", {
                        icon: 2,
                        time: 2000
                    });
                    layer.close(index);
                }
            });
        }, function(index){
            layer.close(index);
        });
    }

    function dd(id){
        layer.confirm("确定给所有用户发送此电子报吗？", {
            btn: ['确定','取消']
        }, function(){
            layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('newsletter.newsletterlog')}}",
                data: {id:id, _token: '{{ csrf_token() }}'},
                type: 'post',
                dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    //成功提示
                    if (resp.code==0) {
                        layer.msg("发送成功!", {
                            icon: 1,
                            time: 1000
                        });
                        $.ajax({
                            url: resp.data,
                            data: {id: id, _token: '{{ csrf_token() }}'},
                            type: 'get',
                            dataType: "json", success: function (resp) {
                            }
                        })

                    } else {
                        //失败提示
                        layer.msg(resp.message, {
                            icon: 2,
                            time: 2000
                        });
                    }
                },error:function(response){
                    layer.msg("请检查网络或权限设置！", {
                        icon: 2,
                        time: 2000
                    });
                    layer.close(index);
                }
            });
        }, function(index){
            layer.close(index);
        });
    }


</script>
