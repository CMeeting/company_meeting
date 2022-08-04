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
                <a style="float: right" href="{{route('mailmagicboard.mailmagic_list')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> New Mailmagicboard</button></a>
            </div>

            <div class="ibox-content">
                <form name="admin_list_sea" class="form-search" method="get" action="{{route('mailmagicboard.mailmagic_list')}}">
                    <div class="col-xs-10 col-sm-5 margintop5">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <select name="query_type" class="form-control" style="width: 115px;">
                                    <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>ID </option>
                                    <option value="titel" @if(isset($query)&&$query['query_type']=='title_h1') selected @endif>Title H1 </option>
                                    <option value="seotitel" @if(isset($query)&&$query['query_type']=='seotitel') selected @endif>SEO Title </option>
                                    <option value="slug" @if(isset($query)&&$query['query_type']=='slug') selected @endif>Slug </option>
                                </select>
                            </div>
                            <input type="text" name="info" class="form-control" value="@if(isset($query)){{$query['info']}}@endif" />
                        </div>
                    </div>
                    <span class="input-group-btn">
											<button type="submit" class="btn btn-purple btn-sm">
												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
												搜索
											</button>
										</span>
                </form>


                <form method="post" action="{{route('mailmagicboard.mailmagic_list')}}" name="form" style="width: 100%;overflow: auto;">

                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th class="text-center" width="100">ID</th>
                            <th>模板名称</th>
                            <th>创建人</th>
                            <th class="text-center">创建时间</th>
                            <th class="text-center" width="150">编辑时间</th>
                            <th class="text-center" width="200">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr id="han_{{$value->id}}">
                                <td class="text-center">{{$value->id}}</td>
                                <td>{{$value->name}}</td>
                                <td class="text-center">{{$value->admin_name}}</td>
                                <td class="text-center">{{$value->craatetime}}</td>
                                <td class="text-center">{{$value->updatetime}}</td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="Edit " href="{{route('documentation.updatesdkDocumentation',$value->id)}}">
                                            <i class="fa fa-edit"></i> preview
                                        </a>
                                        <a class="edit_3 abutton cloros3" style="text-decoration: none;color: #f6fff8" title="Edit " href="{{route('documentation.updatesdkDocumentation',$value->id)}}">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a class="abutton cloros4" style="text-decoration: none;color: #f6fff8" title="Delete" onclick="del('{{$value->id}}')">
                                            <i class="fa fa-trash-o fa-delete"></i>Delete
                                        </a>
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
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消']
        }, function(){
            layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('documentation.delsdkDocumentation')}}",
                data: {delid:id, _token: '{{ csrf_token() }}'},
                type: 'post',
                dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    //成功提示
                    if (resp.code==0) {
                        layer.msg("删除成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            $("#han_"+id).remove();
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
</script>
