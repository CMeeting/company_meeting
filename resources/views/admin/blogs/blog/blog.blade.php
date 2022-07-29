@extends('admin.layouts.layout')
@section('content')
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Blog</h5>
                <a style="float: right" href="{{route('blogs.blogCreate')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Blog</button></a>
            </div>
            <div class="ibox-content">

                <div class="col-xs-10 col-sm-9 margintop5" style="margin-bottom: 5px">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('blogs.blog')}}" style="width: 100%;overflow: auto;">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <select name="query_type" class="form-control" style="display: inline-block;width: 100px;">
                                    <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>ID </option>
                                    <option value="title_h1" @if(isset($query)&&$query['query_type']=='title_h1') selected @endif>title_h1 </option>
                                    <option value="slug" @if(isset($query)&&$query['query_type']=='slug') selected @endif>slug </option>
                                    {{--                                <option value="type_id" @if(isset($query)&&$query['query_type']=='type_id') selected @endif>categories </option>--}}
                                    <option value="title" @if(isset($query)&&$query['query_type']=='title') selected @endif>seo title </option>
                                </select>
                            </div>
                            <input type="text" name="info" class="form-control" style="display: inline-block;width: 150px;" value="@if(isset($query)){{$query['info']}}@endif" />
                            <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                                <select class="form-control"  name="type_id">
                                    <option value="">筛选Category</option>
                                    @foreach($types as $k=>$v)
                                        <option value="{{$k}}" @if(isset($query)&&$query['type_id']==$k) selected @endif>{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="input-group-btn" style="display: inline-block;">
											<button type="submit" class="btn btn-purple btn-sm" style="margin-left: 20px;">
												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
												搜索
											</button>
										</span>
                        </div>

                    </form>
                </div>


                {{--            <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12 but-height">--}}
                {{--                <div class="form-group">--}}

                {{--                    <button type="button"  id="modal_excel" class="form-control btn blue" data-toggle="modal" data-target="#ListStyle" data-placement="top" placeholder="Chee Kin" >--}}
                {{--                        <i class="fa fa-download "></i> 导出--}}
                {{--                    </button>--}}
                {{--                </div>--}}
                {{--            </div>--}}
                <table class="table table-striped table-bordered table-hover m-t-md" style="margin-top: 50px;">
                    <thead>
                    <tr>
                        {{--                    'id','title_h1','slug','categories','tags','seo title','keywords','sort_id','created_at','updated_at'--}}
                        <th class="text-center">ID</th>
                        <th class="text-center">title_h1</th>
                        <th class="text-center">slug</th>
                        <th class="text-center">categories</th>
                        <th class="text-center">tags</th>
                        <th class="text-center">seo title</th>
                        <th class="text-center">keywords</th>
                        <th class="text-center">sort_id</th>
                        <th class="text-center">created_at</th>
                        <th class="text-center">updated_at</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td  class="text-center" >{{$item['id']}}</td>
                            <td>{{$item['title_h1']}}</td>
                            <td>{{$item['slug']}}</td>
                            <td class="text-center">@if(isset($types[$item['type_id']])){{$types[$item['type_id']]}}@endif</td>
                            <td class="text-center">{{$item->tag_id}}</td>
                            <td>{{$item['title']}}</td>
                            <td>{{$item['keywords']}}</td>
                            <td>{{$item['sort_id']}}</td>
                            <td>{{$item['created_at']}}</td>
                            <td>{{$item['updated_at']}}</td>
                            {{--                        <td class="text-center">--}}
                            {{--                            @if($item->status == 1)--}}
                            {{--                                <span class="text-navy">启用</span>--}}
                            {{--                            @else--}}
                            {{--                                <span class="text-danger">禁用</span>--}}
                            {{--                            @endif--}}
                            {{--                        </td>--}}
                            <td class="text-center">
                                <div class="btn-group">
                                    {{--                                <a href="{{route('roles.access',$item->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 权限设置</button></a>--}}
                                    <a href="{{route('blogs.blogEdit',$item['id'])}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                    {{--                                <a href="{{route('blogs.softDel',['blog',$item['id']])}}"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>--}}
                                    <a onclick="del('{{$item['id']}}')"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                    {{--                                <form class="form-common" action="{{ route('roles.destroy', $item->id) }}" method="post">--}}
                                    {{--                                    {{ csrf_field() }}--}}
                                    {{--                                    {{ method_field('DELETE') }}--}}
                                    {{--                                <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>--}}
                                    {{--                                </form>--}}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$data->appends(['info' => isset($query['info'])?$query['info']:'','query_type'=>isset($query['query_type'])?$query['query_type']:'','type_id'=>isset($query['type_id'])?$query['type_id']:''])->links()}}
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
                    data: {table:'blog', id: id},
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