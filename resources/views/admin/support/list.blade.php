@extends('admin.layouts.layout')
@section('content')
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Support</h5>
            <a style="float: right" href="{{route('support.create')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Support</button></a>
        </div>
        <div class="ibox-content">

            <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px">
                <form name="admin_list_sea" class="form-search" method="get" action="{{route('support.list')}}">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <select name="query_type" class="form-control" style="display: inline-block;width: 100px;">
                                <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>ID </option>
                                <option value="version" @if(isset($query)&&$query['query_type']=='version') selected @endif>version_no </option>
                                <option value="order_no" @if(isset($query)&&$query['query_type']=='order_no') selected @endif>order_no </option>
                            </select>
                        </div>
                        <input type="text" name="info" class="form-control" style="display: inline-block;width: 150px;" value="@if(isset($query)){{$query['info']}}@endif" />
                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                            <input type="text"  name="start_date" class="form-control" style="display: inline-block;width: 160px;" id="startDate" placeholder="创建时间--开始" value="@if(isset($query)){{$query['start_date']}}@endif" />
                        </div>
                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                            <input type="text"  name="end_date" class="form-control" style="display: inline-block;width: 160px;" id="endDate" placeholder="创建时间--结束" value="@if(isset($query)){{$query['end_date']}}@endif" />
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

            <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;">
                <thead>
                <tr>
                    <th class="text-center" style="width: 2%">ID</th>
                    <th class="text-center" style="width: 9%">Order_no</th>
                    <th class="text-center" style="width: 4%">version</th>
                    <th class="text-center" style="width: 5%">platform</th>
                    <th class="text-center" style="width: 10%">product</th>
                    <th class="text-center" style="width: 11%">development_language</th>
                    <th class="text-center" style="width: 4%">type</th>
                    <th class="text-center" style="width: 4%">创建人</th>
                    <th class="text-center" style="width: 4%">处理人</th>
                    <th class="text-center" style="width: 6%">status</th>
                    <th class="text-center" style="width: 9%">created_at</th>
                    <th class="text-center" style="width: 9%">updated_at</th>
                    <th class="text-center" style="width: 15%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $item)
                    <tr>
                        <td  class="text-center" >{{$item['id']}}</td>
                        <td  class="text-center" >{{$item['order_no']}}</td>
                        <td>{{$item['version']}}</td>
                        <td>{{$platform[$item['platform']]['name']}}</td>
                        <td class="text-center">{{$platform[$item['product']]['name']}}</td>
                        <td>{{$development_language[$item['development_language']]['title']}}</td>
                        <td>{{$type[$item['type']]}}</td>
{{--                        <td>{{$item['source']}}</td>--}}
{{--                        <td>{{$item['e_mail']}}</td>--}}
                        <td>{{$admins[$item['create_user']]}}</td>
                        <td>{{$admins[$item['handler']]??''}}</td>
                        <td style="color: red;" id="status_{{$item['id']}}">{{$status[$item['status']]}}</td>
                        <td>{{$item['created_at']}}</td>
                        <td>{{$item['updated_at']}}</td>

                        <td class="text-center">
                            <div class="btn-group">
                                @if(1 == $item['status'])
                                <a id="update_{{$item['id']}}" href="{{route('support.edit',$item['id'])}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                @endif
                                @if($item['status']<3)
                                    <a id="status2_{{$item['id']}}" onclick="change_status('{{$item['id']}}','{{$item['status']}}')"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-chain"></i> 更改状态</button></a>
                                    @endif
                                <a onclick="del('{{$item['id']}}')"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$data->appends(['info' => isset($query['info'])?$query['info']:'','query_type'=>isset($query['query_type'])?$query['query_type']:'','platform'=>isset($query['platform'])?$query['platform']:'','start_date'=>isset($query['start_date'])?$query['start_date']:'','end_date'=>isset($query['end_date'])?$query['end_date']:''])->links()}}
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<script>
    var id=0;
    var indexs;
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消']
        }, function(){
            // layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('support.softDel')}}",
                data: {id: id},
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

    layui.use('laydate', function(){
        var laydate = layui.laydate;

        //执行一个laydate实例
        var start = laydate.render({
            elem: '#startDate', //指定元素
            max:1,//最大值为当前日期
            trigger: 'click',
            type: 'datetime',//日期时间选择器
            // value: getRecentDay(-30),//默认值30天前
            done:function(value,date){
                if(value && (value>$("#endDate").val())){
                    /*开始时间大于结束时间时，清空结束时间*/
                    $("#endDate").val("");
                }
                end.config.min ={
                    year:date.year,
                    month:date.month-1,
                    date: date.date,
                    hours:date.hours,//可注释
                    minutes:date.minutes,//可注释
                    seconds:date.seconds//可注释
                };
            }
        });
        var end = laydate.render({
            elem: '#endDate', //指定元素
            max : 1,//最大值为当前日期
            type: 'datetime',//日期时间选择器
            // value: getRecentDay(-1),//默认值昨天
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        });
    });

    function change_status(id){
        id=id
        indexs=layer.open({
            type: 1,
            title: false,
            closeBtn: 1, //不显示关闭按钮
            shade: [0],
            area: ['40%', '55%'],
            anim: 2,
            content: '{!! csrf_field() !!}<div id="change_status_html" class="row"> <div class="col-sm-12"> <div class="ibox-title"> <h5>更改状态</h5> </div> <div class="ibox-content"> <form class="m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" method="post"><input id="change_status_id" type="hidden" class="form-control" name="id" value="'+id+'">  <div class="hr-line-dashed m-t-sm m-b-sm"></div> <div class="form-group"> <label class="col-sm-3 control-label">邮件模板：</label> <div class="input-group col-sm-8"> <select class="form-control" name="demo" class="select"> <option value="0">请选择需要发送的邮件模板</option>@foreach ($email as $k=>$vs)<option value="{{$vs['id']}}">{{$vs['name']}}</option>@endforeach</select> </div> </div><div class="form-group"><label class="col-sm-3 control-label">备注：</label><div class="input-group col-sm-8"><textarea rows=5 cols=40 name="info"></textarea></div></div><div class="form-group" style="text-align: center">多条备注用英文分号;隔开，系统会自动生成多条备注展示前端</div> <div class="hr-line-dashed m-t-sm m-b-sm"></div> <div class="form-group"> <div class="col-sm-10 col-sm-offset-2"> <a class="btn btn-primary" onclick="sub()"><i class="fa fa-check"></i>&nbsp;保 存</a>　<button class="btn btn-white reset" type="reset"><i class="fa fa-repeat"></i> 重 置</button> </div> </div> <div class="clearfix"></div></form> </div> </div> </div>'
        });
    }


    function sub(){
        var form_data = new FormData($("#form_data")[0]);
        form_data.set("_token",'{{ csrf_token() }}');
        layer.close(index);
        var index = layer.load();
        $.ajax({
            url:"{{route('support.changeStatus')}}",
            processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
            contentType:false,
            type:"post",
            data: form_data,
            success:function(data){
                if(data.code==1){
                    layer.close(index);
                    layer.close(indexs);
                    layer.msg("更新成功", {time: 1500, anim: 1});
                    if(data.status==2){
                        $("#status_"+data.id).html("已接收");
                        $("#update_"+data.id).remove();
                    }else if(data.status==3){
                        $("#status_"+data.id).html("已解决");
                        $("#status2_"+data.id).remove();
                    }
                }else{
                    layer.close(index);
                    layer.msg(data.msg, {time: 1500, anim: 6});
                    return false;
                }
            }, error:function(ret){

            }
        })

    }
</script>
@endsection