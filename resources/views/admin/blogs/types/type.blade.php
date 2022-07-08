@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Categories</h5>
            <a style="float: right" href="{{route('blogs.typeCreate')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Category</button></a>
        </div>
        <div class="ibox-content">

            <div class="col-xs-10 col-sm-5 margintop5" style="margin-bottom: 5px">
                <form name="admin_list_sea" class="form-search" method="get" action="{{route('blogs.types')}}">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <select name="query_type" class="form-control" style="width: 115px;">
                                <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>ID </option>
                                <option value="title" @if(isset($query)&&$query['query_type']=='title') selected @endif>title </option>
                                <option value="slug" @if(isset($query)&&$query['query_type']=='slug') selected @endif>slug </option>
                                <option value="keywords" @if(isset($query)&&$query['query_type']=='keywords') selected @endif>keywords </option>
                                <option value="seo_title" @if(isset($query)&&$query['query_type']=='seo_title') selected @endif>seo title </option>
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

            <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;">
                <thead>
                <tr>
                    <th class="text-center" style="width: 4%">ID</th>
                    <th class="text-center" style="width: 10%">title</th>
                    <th class="text-center" style="width: 10%">slug</th>
                    <th class="text-center" style="width: 10%">seo title</th>
                    <th class="text-center" style="width: 10%">keywords</th>
                    <th class="text-center" style="width: 10%">description</th>
                    <th class="text-center" style="width: 5%">sort_id</th>
                    <th class="text-center" style="width: 8%">created_at</th>
                    <th class="text-center" style="width: 8%">updated_at</th>
                    <th class="text-center" style="width: 10%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $item)
                    <tr>
                        <td  class="text-center" >{{$item->id}}</td>
                        <td>{{$item->title}}</td>
                        <td class="text-center">{{$item->slug}}</td>
                        <td>{{$item->seo_title}}</td>
                        <td>{{$item->keywords}}</td>
                        <td>{{$item->description}}</td>
                        <td>{{$item->sort_id}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->updated_at}}</td>

                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{route('blogs.typeEdit',$item->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                <a href="{{route('blogs.softDel',['type',$item->id])}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
@endsection