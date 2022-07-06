@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Tags</h5>
        </div>
        <div class="ibox-content">
            <a href="{{route('blogs.tagCreate')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Tag</button></a>
            <table class="table table-striped table-bordered table-hover m-t-md">
                <thead>
                <tr>
{{--                    'id','title_h1','slug','categories','tags','seo title','keywords','sort_id','created_at','updated_at'--}}
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
                                <a href="{{route('blogs.softDel',['tag',$item->id])}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
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