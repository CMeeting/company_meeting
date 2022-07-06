@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Blog</h5>
        </div>
        <div class="ibox-content">
            <a href="{{route('blogs.blogCreate')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Blog</button></a>
            <div class="col-xs-10 col-sm-5 margintop5">
                <form name="admin_list_sea" class="form-search" method="get" action="{{route('blogs.blog')}}">
                    <div class="input-group">
										<span class="input-group-addon">
											<i class="ace-icon fa fa-check"></i>
										</span>
                        <input type="text" name="slug" class="form-control" value="" placeholder="输入需查询的slug" />
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
            <table class="table table-striped table-bordered table-hover m-t-md">
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
                        <td class="text-center">{{$types[$item['type_id']]}}</td>
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
                                <a href="{{route('blogs.softDel',['blog',$item['id']])}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
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
            {{$data->links()}}
        </div>
    </div>
    <div class="clearfix"></div>
</div>
@endsection