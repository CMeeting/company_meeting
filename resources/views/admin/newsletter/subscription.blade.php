@extends('admin.layouts.layout')
@section('content')
    <style>
        .abutton{
            text-align: center;
            display: inline-block;
            border-radius: 10px;
            border: 1px solid lavenderblush;
            margin-right: 3px;
            width: 91px;
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
                <h5>Subscription</h5>
                <a style="float: right" href="{{route('newsletter.createsubscription')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> New Subscription</button></a>
            </div>

            <div class="ibox-content">

                <form method="post" action="{{route('newsletter.subscription_list')}}" name="form" style="width: 100%;overflow: auto;">

                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th class="text-center" width="100">ID</th>
                            <th>邮件地址</th>
                            <th>订阅类型</th>
                            <th>订阅状态</th>
                            <th class="text-center">订阅时间</th>
                            <th class="text-center">更新时间</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr id="han_{{$value->id}}">
                                <td class="text-center">{{$value->id}}</td>
                                <td style="word-break: break-word;">{{$value->email}}</td>
                                <td style="word-break: break-word;">
                                    @if($value->admin_id==0)
                                        <a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: green" >
                                         用户订阅
                                        </a>
                                    @else
                                        <a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: blue" >
                                        系统添加
                                        </a>
                                    @endif</td>
                                <td class="text-center" id="dy_{{$value->id}}">
                                    @if($value->status==1)
                                        <a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: green" >
                                            订阅状态
                                        </a>
                                    @else
                                        <a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: red" >
                                            取消订阅
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">{{$value->created_at}}</td>
                                <td class="text-center">{{$value->updated_at}}</td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <a class="edit_3 abutton cloros3" style="text-decoration: none;color: #f6fff8" title="编辑 " href="{{route('newsletter.updatesubscription',$value->id)}}">
                                            <i class="fa fa-edit"></i> 编辑
                                        </a>
                                        <span id="deleted_{{$value->id}}">
                                        @if($value->status==1)
                                        <a class="abutton cloros4" style="text-decoration: none;color: #f6fff8" title="当前订阅状态" onclick="del('{{$value->id}}',{{$value->status}})">
                                            <i class="fa fa-chain fa-delete"></i>取消订阅
                                        </a>
                                        @else
                                        <a class="abutton clorosx" style="text-decoration: none;color: #f6fff8;background: green" title="当前取消订阅状态" onclick="del('{{$value->id}}',{{$value->status}})">
                                            <i class="fa fa-chain fa-delete"></i>订阅状态
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
        if(status==1){
            var title="您确定取消订阅吗？";
        }else{
            var title="您确定订阅吗？";
        }
        layer.confirm(title, {
            btn: ['确定','取消']
        }, function(){
            layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('newsletter.toggle_status')}}",
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
                            if(resp.status==0){
                                $("#deleted_"+id).html('<a class="abutton clorosx" style="text-decoration: none;color: #f6fff8;background: green" title="当前取消订阅状态" onclick="del('+id+','+resp.status+')"><i class="fa fa-chain fa-delete"></i>订阅状态 </a>');
                                $("#dy_"+id).html('<a class="abutton cloros2" style="text-decoration: none;color:#f6fff8;background: red" >取消订阅 </a>');
                            }else{
                                $("#deleted_"+id).html('<a class="abutton cloros4" style="text-decoration: none;color: #f6fff8" title="当前订阅状态" onclick="del('+id+','+resp.status+')"><i class="fa fa-chain fa-delete"></i>取消订阅</a>');
                                $("#dy_"+id).html('<a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: green">订阅状态 </a>');
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



</script>
