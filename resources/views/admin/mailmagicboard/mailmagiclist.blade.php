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
                <h5>Mailmagicboard</h5>
                <a style="float: right" href="{{route('mailmagicboard.createmailmagiclist')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> New Mailmagicboard</button></a>
            </div>

            <div class="ibox-content">
{{--                <form name="admin_list_sea" class="form-search" method="get" action="{{route('mailmagicboard.mailmagic_list')}}">--}}
{{--                    <div class="col-xs-10 col-sm-5 margintop5">--}}
{{--                        <div class="input-group">--}}
{{--                            <div class="input-group-btn">--}}
{{--                                <select name="query_type" class="form-control" style="width: 115px;">--}}
{{--                                    <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>ID </option>--}}
{{--                                    <option value="titel" @if(isset($query)&&$query['query_type']=='title_h1') selected @endif>Title H1 </option>--}}
{{--                                    <option value="seotitel" @if(isset($query)&&$query['query_type']=='seotitel') selected @endif>SEO Title </option>--}}
{{--                                    <option value="slug" @if(isset($query)&&$query['query_type']=='slug') selected @endif>Slug </option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <input type="text" name="info" class="form-control" value="@if(isset($query)){{$query['info']}}@endif" />--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <span class="input-group-btn">--}}
{{--											<button type="submit" class="btn btn-purple btn-sm">--}}
{{--												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>--}}
{{--												搜索--}}
{{--											</button>--}}
{{--										</span>--}}
{{--                </form>--}}


                <form method="post" action="{{route('mailmagicboard.mailmagic_list')}}" name="form" style="width: 100%;overflow: auto;">

                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th class="text-center" width="100">ID</th>
                            <th>模板名称</th>
                            <th>邮件标题</th>
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
                                                <a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="测试邮件发送" onclick="dd({{$value->id}})">
                                                <i class="fa fa-newspaper-o"></i> 测试
                                                </a>
                                            @endif
                                        </span>

                                        <a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="预览 " href="{{route('mailmagicboard.mailmagiclist_info',$value->id)}}">
                                            <i class="fa fa-users"></i> 预览
                                        </a>
                                        <a class="edit_3 abutton cloros3" style="text-decoration: none;color: #f6fff8" title="编辑 " href="{{route('mailmagicboard.updatemailmagiclist',$value->id)}}">
                                            <i class="fa fa-edit"></i> 编辑
                                        </a>
                                        <span id="deleted_{{$value->id}}">
                                        @if($value->deleted==0)
                                        <a class="abutton cloros4" style="text-decoration: none;color: #f6fff8" title="当前启用状态" onclick="del('{{$value->id}}',{{$value->deleted}})">
                                            <i class="fa fa-trash-o fa-delete"></i>禁用
                                        </a>
                                        @else
                                        <a class="abutton clorosx" style="text-decoration: none;color: #f6fff8" title="当前禁用状态" onclick="del('{{$value->id}}',{{$value->deleted}})">
                                            <i class="fa fa-trash-o fa-delete"></i>启用
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
                url: "{{route('mailmagicboard.delmailmagic')}}",
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
                                $("#deleted_"+id).html('<a class="abutton clorosx" style="text-decoration: none;color: #f6fff8" title="当前禁用状态" onclick="del('+id+','+resp.status+')"><i class="fa fa-trash-o fa-delete"></i>启用 </a>');
                            }else{
                                $("#span_"+id).html('<a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="测试邮件发送" onclick="dd('+id+')"><i class="fa fa-newspaper-o"></i> 测试 </a>');
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

    function show(id){
        var index = layer.load();
        $.ajax({
            url: "{{route('documentation.showHideclassification')}}",
            data: {id:id,type:'sdk_documentation', _token: '{{ csrf_token() }}'},
            type: 'post',
            dataType: "json",
            success: function (resp) {
                if (resp.code==0) {
                    if(resp.status==1){
                        var htmls='<a type="button" style="text-decoration: none;color: #f6fff8"   data-id="{$v.id}"  class="openBtn_'+id+' abutton cloros" data-style="zoom-out" onclick="show('+id+');"> <span class="ladda-label">show</span></a>';
                    }else{
                        var htmls='<a type="button" style="text-decoration: none;color: #f6fff8"  data-id="{$v.id}"  class="openBtn_'+id+' abutton cloros1" data-style="zoom-out" onclick="show('+id+');"> <span class="ladda-label">hide</span></a>';
                    }
                    $(".open_"+id).html(htmls);
                    layer.close(index);
                } else {
                    //失败提示
                    layer.msg(resp.msg, {
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
    }
    function dd(id){
        layer.open({
            id:1,
            type: 1,
            title:'测试发送邮件内容',
            skin:'layui-layer-rim',
            area:['450px', 'auto'],
            closeBtn :1,//右上角的关闭按钮取消
            content: ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
                +'<div class="col-sm-12" style="margin-top: 10px">'
                +'<div class="input-group">'
                +'<span class="input-group-addon">输入接收邮箱:</span>'
                +'<input id="secondpwd" type="text" class="form-control" placeholder="请输入正确邮箱格式">'
                +'</div>'
                +'</div>'
                +'</div>'
            ,
            btn:['发送'],
            btn1: function (index,layero) {
                var secondpwd = $('#secondpwd').val();
                var e= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if(secondpwd==""){
                    layer.msg('邮件地址不能为空', {time: 1500, anim: 6});
                }else{
                    if(!e.test(secondpwd)){
                        layer.msg('邮件地址不合法', {time: 1500, anim: 6});
                        return false;
                    }
                    var index = layer.load();
                    $.post("{{route('mailmagicboard.send_email')}}", { id:id,email:secondpwd, _token: '{{ csrf_token() }}'},
                        function(data){
                            if(data.code==1){
                                layer.close(index);
                                layer.msg("发送成功", {time: 1500, icon: 1});
                            }else{
                                layer.close(index);
                                layer.msg("发送失败", {time: 1500, anim: 6});

                            }
                        });
                }
            }
        });
    }


</script>
