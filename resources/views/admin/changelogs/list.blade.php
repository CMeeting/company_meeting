@extends('admin.layouts.layout')
@section('content')
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Changelogs</h5>
            <a style="float: right" href="{{route('changelogs.create')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Changelogs</button></a>
        </div>
        <div class="ibox-content">

            <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px;padding-left: 0">
                <form name="admin_list_sea" class="form-search" method="get" action="{{route('changelogs.list')}}">
                    <div class="input-group" style="margin-left: 0 auto">
                        <div class="input-group-btn" style="vertical-align: top;">
                            <select name="query_type" class="form-control" style="display: inline-block;width: 120px;">
                                <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>ID </option>
                                <option value="version_no" @if(isset($query)&&$query['query_type']=='version_no') selected @endif>version_no </option>
                                <option value="slug" @if(isset($query)&&$query['query_type']=='slug') selected @endif>slug </option>
                            </select>
                        </div>
                        <input type="text" name="info" class="form-control" style="display: inline-block;width: 200px;" value="@if(isset($query)){{$query['info']}}@endif" />
                        <div class="input-group-btn" style="display: inline-block;width: 120px;margin-left:10px;">
                            <select class="form-control"  name="platform">
                                <option value="-1">筛选 Platform</option>
                                @foreach($platformid as $k=>$v)
                                    <option value="{{$k}}" @if(isset($query)&&$query['platform']==$k) selected @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group-btn" style="display: inline-block;width: 230px;margin-left:10px;">
                            <select class="form-control"  name="product">
                                <option value="-1">筛选 product</option>
                                @foreach($version as $k=>$v)
                                    <option value="{{$k}}" @if(isset($query)&&$query['product']==$k) selected @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:10px;">
                            <select class="form-control"  name="development_language">
                                <option value="-1">筛选 language</option>
                                @foreach($development_language as $k=>$v)
                                    <option value="{{$k}}" @if(isset($query)&&$query['development_language']==$k) selected @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:10px;">
                            <input type="text"  name="start_date" class="form-control" style="display: inline-block;width: 160px;" id="startDate" placeholder="更新时间-开始" value="@if(isset($query)){{$query['start_date']}}@endif" />
                        </div>
                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                            <input type="text"  name="end_date" class="form-control" style="display: inline-block;width: 160px;" id="endDate" placeholder="更新时间-结束" value="@if(isset($query)){{$query['end_date']}}@endif" />
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
                    <th class="text-center" style="width: 5%" title="platform">platform</th>
                    <th class="text-center" style="width: 10%" title="product">product</th>
                    <th class="text-center" style="width: 5%" title="language">language</th>
                    <th class="text-center" style="width: 5%">version</th>
                    <th class="text-center" style="width: 10%">slug</th>
                    <th class="text-center" style="width: 10%">seo title</th>
                    <th class="text-center" style="width: 3%">order</th>
                    <th class="text-center" style="width: 8%">change_date</th>
                    <th class="text-center" style="width: 8%">updated_at</th>
                    <th class="text-center" style="width: 8%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $item)
                    <tr>
                        <td  class="text-center" >{{$item['id']}}</td>
                        <td  class="text-center" >{{$platform[$item['platform']]['name']}}</td>
                        <td  class="text-center" >{{$platform[$item['product']]['name']}}</td>
                        <td  class="text-center" >{{$development_language[$item['development_language']]}}</td>
                        <td  class="text-center" >{{$item['version_no']}}</td>
                        <td  class="text-center" >{{$item['slug']}}</td>
                        <td  class="text-center" >{{$item['seo_title']}}</td>
                        <td  class="text-center" >{{$item['order_num']}}</td>
                        <td  class="text-center" >{{$item['change_date']}}</td>
                        <td  class="text-center" >{{$item['updated_at']}}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{route('changelogs.edit',$item['id'])}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
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
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消']
        }, function(){
            // layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "{{route('changelogs.softDel')}}",
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
            type: 'day',//日期时间选择器
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
            max : 30,//最大值为当前日期
            type: 'datetime',//日期时间选择器
            // value: getRecentDay(-1),//默认值昨天
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        });
    });
</script>
@endsection