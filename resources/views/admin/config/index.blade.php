@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            @foreach($showKeyArr as $key=>$name)
            <button class="btn btn-primary btn-sm" 
                    @if($key==$checkKey)
                    style="background-color:#5440ff;border-color:#5440ff" 
                    @else
                    style="background-color:#d5d4d4;border-color:#d5d4d4" 
                    @endif
                    type="button"><a href="{{route('config.index',["key"=>$key])}}" link-url="javascript:void(0)" style="color:#fffcfc">{{$name}}</a></button>
            @endforeach
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="{{route('config.create',$checkKey)}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加{{$showKeyArr[$checkKey]}}</button></a>
            <form method="post" action="{{route('config.index')}}" name="form">
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        @if($checkKey=="pfid")
                        <th>用户渠道ID</th>
                        <th>用户渠道名字</th>
                        @elseif($checkKey=="usid")
                        <th>厂商渠道ID</th>
                        <th>厂商渠道名字</th>
                        @endif
                        <th class="text-center" width="200">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $k => $item)
                        <?php $itemArr =$item->toArray()?>
                        <tr>
                            <td class="text-center">{{$itemArr['id']}}</td>
                            <td class="text-center">{{$itemArr['type']}}</td>
                            <td class="text-center">{{$itemArr['value']}}</td>
                            
                            <td class="text-center">
                                <div class="btn-group">
                                    
                                    <a href="{{route('config.create',["key"=>$checkKey,"id"=>$itemArr['id']])}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                    <a href="{{route('config.delete',$itemArr['id'])}}"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
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