@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Tags</h5>
            <a style="float: right" href="{{route('blogs.tagCreate')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Tag</button></a>
        </div>
        <div class="ibox-content">


            <div class="col-xs-10 col-sm-5 margintop5" style="margin-bottom: 5px">
                <form name="admin_list_sea" class="form-search" method="get" action="{{route('blogs.tags')}}">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <select name="query_type" class="form-control" style="width: 115px;">
                                <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>ID </option>
                                <option value="title" @if(isset($query)&&$query['query_type']=='title') selected @endif>title </option>
                                <option value="sort_id" @if(isset($query)&&$query['query_type']=='sort_id') selected @endif>sort_id </option>
                            </select>
                        </div>
                        <input type="text" name="info" class="form-control" value="@if(isset($query)){{$query['info']}}@endif" />
                        <span class="input-group-btn">
											<button type="submit" class="btn btn-purple btn-sm">
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
                    <th class="text-center">ID</th>
                    <th class="text-center">title</th>
                    <th class="text-center">sort_id</th>
                    <th class="text-center">created_at</th>
                    <th class="text-center">updated_at</th>
                    <th class="text-center">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $item)
                    <tr>
                        <td  class="text-center" >{{$item->id}}</td>
                        <td>{{$item->title}}</td>
                        <td>{{$item->sort_id}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->updated_at}}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{route('blogs.tagEdit',$item->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
{{--                                <a href="{{route('blogs.softDel',['tag',$item->id])}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>--}}
                                <a onclick="del('{{$item->id}}')"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$data->appends(['info' => isset($query['info'])?$query['info']:'','query_type'=>isset($query['query_type'])?$query['query_type']:''])->links()}}
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
                url: "{{route('blogs.softDel')}}",
                data: {table:'tag', id: id},
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
                        layer.msg(resp.msg, {
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
</script>
@endsection