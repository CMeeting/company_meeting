@extends('admin.layouts.layout')
@section('content')
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/bootstrap/bootstrap-select.css"/>

    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <script src="/js/bootstrap/bootstrap.min.js"></script>
    <script src="/js/bootstrap/countrypicker.min.js"></script>
    <script src="/js/bootstrap/bootstrap-select.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>


    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>用户管理</h5>
                <div class="btn-group" style="display: inline-block; float: right">
                    <a style="" href="{{route('user.list')}}" link-url="javascript:void(0)"><button class="btn layui-btn-warm btn-sm" type="button">刷新</button></a>
                    <a style="" href="{{route('user.create')}}" link-url="javascript:void(0)"><button class="btn layui-btn-danger btn-sm" type="button"><i class="fa fa-paste"></i>添加</button></a>
                    <button id="export" class="btn layui-btn-primary btn-sm" type="button" style="float: right"><i class="fa fa-paste"></i>导出数据</button>
                </div>
            </div>
            <div class="ibox-content">

                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px;padding-left: 0">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('user.list')}}">
                        {{ csrf_field() }}
                        <div class="input-group">
                            <div class="layui-form-item" style="display: inline-block">
                                <label class="layui-form-label" style="width: 90px">用户筛选</label>
                                <div class="input-group-btn" style="display: inline-block;width: 200px;">
                                    <input id="keyword" type="text" name="keyword" class="form-control" style="display: inline-block;width: 240px;" value="@if(isset($query['keyword'])){{$query['keyword']}}@endif" placeholder="用户ID/邮箱/Full Name/Company"/>
                                </div>
                            </div>

                            <div class="layui-form-item" style="display: inline-block; margin-left: 50px">
                                <div class="form-group">
                                    <label class="layui-form-label" style="width: 70px">Country</label>
                                    <select id="country" name="country" class="selectpicker countrypicker" data-live-search="true" data-default="{{array_get($query, 'country', 'All')}}" data-flag="true"></select>
                                </div>
                            </div>

                            <div class="layui-form-item" style="display: inline-block; margin-left: 100px">
                                <label class="layui-form-label" style="width: 90px">用户类型</label>
                                <div class="input-group-btn" style="display: inline-block;width: 120px;">
                                    <select id="type" class="form-control"  name="type">
                                        <option value="-1">用户类型</option>
                                        @foreach($type_arr as $key=>$type)
                                            <option value={{$key}} @if(isset($query['type'])&&$query['type']==$key) selected @endif>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item" style="display: inline-block; margin-left: 20px">
                                <label class="layui-form-label" style="width: 90px">注册时间</label>
                                <div class="input-group-btn" style="display: inline-block;width: 150px;">
                                    <input type="text"  name="start_date" class="form-control" style="display: inline-block;width: 160px;" id="startDate" placeholder="注册时间-开始" value="@if(isset($query['start_date'])){{$query['start_date']}}@endif" />
                                </div>
                                <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                                    <input type="text"  name="end_date" class="form-control" style="display: inline-block;width: 160px;" id="endDate" placeholder="注册时间-结束" value="@if(isset($query['end_date'])){{$query['end_date']}}@endif" />
                                </div>
                                <span class="input-group-btn" style="display: inline-block;">
                                <button type="submit" class="btn btn-purple btn-sm" style="margin-left: 20px;">
                                    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                    搜索
                                </button>

                                </span>
                            </div>
                        </div>
                    </form>
                </div>

                <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 3%">用户ID</th>
                        <th class="text-center" style="width: 5%">Email</th>
                        <th class="text-center" style="width: 10%">Full Name</th>
                        <th class="text-center" style="width: 5%">Company</th>
                        <th class="text-center" style="width: 5%">Country</th>
                        <th class="text-center" style="width: 10%">用户类型</th>
                        <th class="text-center" style="width: 10%">消费金额</th>
                        <th class="text-center" style="width: 3%">订单数量</th>
                        <th class="text-center" style="width: 8%">注册时间</th>
                        <th class="text-center" style="width: 8%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key=>$item)
                        <tr>
                            <td  class="text-center" >{{$item->uid}}</td>
                            <td  class="text-center" >{{$item->u_email}}</td>
                            <td  class="text-center" >{{$item->full_name}}</td>
                            <td  class="text-center" >{{$item->company ?? '-'}}</td>
                            <td  class="text-center" >{{$item->country ?? '-'}}</td>
                            <td  class="text-center" >{{$type_arr[$item->type]}}</td>
                            <td  class="text-center" >{{$item->order_amount ?? '-'}}</td>
                            <td  class="text-center" >{{$item->order_num}}</td>
                            <td  class="text-center" >{{$item->register_time}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{route('user.detail', $item->uid)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 查看</button></a>
                                    <a href="{{route('user.edit', $item->uid)}}"><button class="btn layui-btn-normal btn-xs" type="button"><i class="fa fa-paste"></i> 编辑</button></a>
                                </div>
                            </td>
                        </tr>
                        <a id="donload"></a>
                    @endforeach
                    </tbody>
                </table>
                {{$data->appends(['keyword' =>isset($query['keyword']) ? $query['keyword'] : '','country'=>isset($query['country']) ? $query['country'] : '','type'=>isset($query['type']) ? $query['type'] : '','start_date'=>isset($query['start_date']) ? $query['start_date'] : '','end_date'=>isset($query['end_date']) ? $query['end_date'] : ''])->links()}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script>
        layui.use('laydate', function(){
            let laydate = layui.laydate;

            //执行一个laydate实例
            let start = laydate.render({
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
            let end = laydate.render({
                elem: '#endDate', //指定元素
                max : 30,//最大值为当前日期
                type: 'day',//日期时间选择器
                // value: getRecentDay(-1),//默认值昨天
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });

        $("#export").click(function () {
            html =  '<div style="display: flex; justify-content: left;flex-wrap: wrap; padding: 10px">' +
                    '<div style="margin-bottom: 20px"><label style="margin-right: 10px; width: 100px"><input name="id"  type="checkbox"  value="uid" checked="checked"/>用户id</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="email"  type="checkbox"  value="u_email" checked="checked"/>Email</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="full_name"  type="checkbox"  value="full_name" checked="checked"/>Full Name</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="company"  type="checkbox"  value="company" checked="checked"/>Company</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="country"  type="checkbox"  value="country" checked="checked"/>Country</label></div>' +
                    '<div><label style="margin-right: 10px; width: 100px"><input name="type"  type="checkbox"  value="type" checked="checked"/>用户类型</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="order_price"  type="checkbox"  value="order_amount" checked="checked"/>消费金额</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="order_number"  type="checkbox"  value="order_num" checked="checked"/>订单数量</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="created_at"  type="checkbox"  value="register_time" checked="checked"/>注册时间</label></div></div>';

            layer.open({
                type: 1,
                title: false,
                closeBtn: 1, //不显示关闭按钮
                shade: [0],
                anim: 2,
                content: html,
                // content: "<pre>"+data+"</pre>"
                btn:['确定'],
                area: ['600px', '150px'],
                btn1: function () {
                    let field = [];
                    $("input").each(function (){
                        if($(this).is(':checked')){
                           field.push($(this).val())
                        }
                    })

                    let keyword = $('#keyword').val()
                    let country = $('#country').val()
                    let type = $('#type').find("option:selected").val()
                    let startDate = $('#startDate').val()
                    let endDate = $('#endDate').val()

                    location.href = "/admin/user/list?export=1" + keyword + "&country=" + country + "&type=" + type + "&start_date=" + startDate + "&end_date=" + endDate
                        + "&field=" + field.join(',');
                }
            });
        });
    </script>
@endsection
