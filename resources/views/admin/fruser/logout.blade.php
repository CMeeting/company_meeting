@extends('admin.layouts.layout')
@section('content')
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>注销用户</h5>
            </div>
            <div class="ibox-content">

                <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 5%">Email</th>
                        <th class="text-center" style="width: 10%">Full Name</th>
                        <th class="text-center" style="width: 8%">注册时间</th>
                        <th class="text-center" style="width: 8%">注销时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key=>$item)
                        <tr>
                            <td  class="text-center" >{{$item->email}}</td>
                            <td  class="text-center" >{{$item->full_name}}</td>
                            <td  class="text-center" >{{$item->register_time}}</td>
                            <td  class="text-center" >{{$item->created_at}}</td>
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
