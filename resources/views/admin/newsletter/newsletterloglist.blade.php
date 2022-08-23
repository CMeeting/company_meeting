@extends('admin.layouts.layout')
@section('content')
    <style>
        .abutton{
            display: inline-block;
            border-radius: 10px;
            text-align: center;
            border: 1px solid lavenderblush;
            margin-right: 3px;
            width: 100px;
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
                <h5>Newsletter_log</h5>
            </div>

            <div class="ibox-content">

                <form method="post" action="{{route('newsletter.newsletterloglist')}}" name="form" style="width: 100%;overflow: auto;">

                    <table class="table table-striped table-bordered table-hover m-t-md">
                        <thead>
                        <tr>
                            <th class="text-center" width="100">ID</th>
                            <th>电子报模板ID</th>
                            <th>接收人邮箱</th>
                            <th>发送状态</th>
                            <th class="text-center">创建时间</th>
                            <th class="text-center">发送时间</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr id="han_{{$value->id}}">
                                <td class="text-center">{{$value->id}}</td>
                                <td class="text-center">{{$value->association_id}}</td>
                                <td style="word-break: break-word;">{{$value->mail}}</td>
                                <td class="text-center" id="dy_{{$value->id}}">
                                    @if($value->status==1)
                                        <a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: green" >
                                            发送成功
                                        </a>
                                    @elseif($value->status==2)
                                        <a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: red" >
                                            发送失败
                                        </a>
                                    @else
                                        <a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: blue" >
                                            待发送
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">{{$value->created_at}}</td>
                                <td class="text-center">{{$value->updated_at}}</td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8" title="预览内容详情" href="{{route('newsletter.newsletterloginfo',$value->id)}}">
                                            <i class="fa fa-users"></i> 详情
                                        </a>
                                        <span id="span_{{$value->id}}">
                                                <a class="edit_3 abutton cloros2" style="text-decoration: none;color: #f6fff8;background: green" title="重新发送电子报" onclick="dd({{$value->id}})">
                                                <i class="fa fa-newspaper-o"></i> 重新发送
                                                </a>
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

    function dd(id){
        layer.confirm("确定给该用户重新发送此电子报吗？", {
            btn: ['确定','取消']
        }, function(){
            layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('newsletter.again_sendfind')}}",
                data: {id:id, _token: '{{ csrf_token() }}'},
                type: 'post',
                dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    //成功提示
                    if (resp.code==1) {
                        layer.msg("发送成功!", {
                            icon: 1,
                            time: 1000
                        });
                     $("#dy_"+id).html('<a class="abutton cloros2" style="text-decoration: none;color: #f6fff8;background: green" >发送成功 </a>');

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
