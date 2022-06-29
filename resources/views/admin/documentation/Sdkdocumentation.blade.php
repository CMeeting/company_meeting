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
            <h5>Documentation</h5>
        </div>

        <div class="ibox-content">
            <a href="{{route('documentation.createsdkDocumentation')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> New Sdkdocumentation</button></a>
            <form method="post" action="{{route('documentation.sdkDocumentation')}}" name="form">

                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th>Title H1</th>
                        <th>SEO Title</th>
                        <th class="text-center">Slug</th>
                        <th class="text-center" width="150">platformversion</th>
                        <th class="text-center" width="150">classification</th>
                        <th class="text-center" width="150">created_at</th>
                        <th class="text-center" width="80">updated_at</th>
                        <th class="text-center" width="200">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['data'] as $key => $value)
                        <tr id="han_{{$value->id}}">
                            <td class="text-center">{{$value->id}}</td>
                            <td>{{$value->titel}}</td>
                            <td class="text-center">{{$value->seotitel}}</td>
                            <td class="text-center">{{$value->slug}}</td>
                            <td class="text-center">{{$value->platformversion}}</td>
                            <td class="text-center">{{$value->classification}}</td>
                            <td class="text-center">{{$value->created_at}}</td>
                            <td class="text-center">{{$value->updated_at}}</td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <font class="open_{{$value->id}}">
                                        @if($value->enabled == 1)
                                        <a type="button" style="text-decoration: none;color: #f6fff8"   data-id="{$v.id}"  class="openBtn_{{$value->id}} abutton cloros" data-style="zoom-out" onclick="show({{$value->id}});">
                                            <span class="ladda-label">show</span>
                                        </a>
                                        @else
                                        <a type="button" style="text-decoration: none;color: #f6fff8"  data-id="{$v.id}"  class="openBtn_{{$value->id}} abutton cloros1" data-style="zoom-out" onclick="show({{$value->id}});">
                                            <span class="ladda-label">hide</span>
                                        </a>
                                        @endif
                                    </font>
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
                {{$data->links()}}
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
            }
        });
    }
</script>