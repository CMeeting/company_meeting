@extends('admin.layouts.layout')
@section('content')
    <style>
        .abutton {
            display: inline-block;
            border-radius: 10px;
            border: 1px solid lavenderblush;
            margin-right: 3px;
            width: 60px;
            text-align: center;
            padding: 0px 5px 0px 5px;
            text-decoration: none;
            color: #f6fff8;
        }

        .cloros {
            background-color: #20e281;
        }

        .cloros1 {
            background-color: #e2322d;
        }
        .cloros2{
            background-color: #0b94ea;
        }
    </style>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Order</h5>
                <button id="export" class="btn layui-btn-primary btn-sm" type="button" style="float: right;margin-left: 5px"><i class="fa fa-paste"></i>导出数据</button>
                <a style="float: right;margin-left: 5px" href="{{route('order.create')}}" link-url="javascript:void(0)">
                    <button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Order</button>
                </a>
            </div>
            <div class="ibox-content">

                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('order.index')}}">
                        <div class="input-group">

                            <div class="input-group-btn" style="display: block;">
                                <select id="query_type" name="query_type" class="form-control"
                                        style="display: inline-block;width: 100px;">
                                    <option value="orders.id" @if(isset($query)&&$query['query_type']=='orders.id') selected @endif>
                                        ID
                                    </option>
                                    <option value="orders.order_no" @if(isset($query)&&$query['query_type']=='orders.order_no') selected @endif>
                                        订单号
                                    </option>
                                </select>
                            </div>
                            <input id="info" type="text" name="info" placeholder="请输入筛选内容" class="form-control" style="display: inline-block;width: 150px;
                                   value="@if(isset($query['info']))value="{{$query['info']}}"@endif"/>


                            <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <select id="status" class="form-control"  name="status" tabindex="1">
                                        <option value="">订单状态</option>
                                        <option value="1" @if(isset($query)&&$query['status']==1) selected @endif>待支付</option>
                                        <option value="2" @if(isset($query)&&$query['status']==2) selected @endif>已支付</option>
                                        <option value="3" @if(isset($query)&&$query['status']==3) selected @endif>已完成</option>
                                        <option value="4" @if(isset($query)&&$query['status']==4) selected @endif>待退款</option>
                                        <option value="5" @if(isset($query)&&$query['status']==5) selected @endif>已关闭</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <select id="status" class="form-control"  name="details_type" tabindex="1">
                                        <option value="">订单类型</option>
                                        <option value="1" @if(isset($query)&&$query['details_type']==1) selected @endif>SDK试用</option>
                                        <option value="2" @if(isset($query)&&$query['details_type']==2) selected @endif>SDK订单</option>
                                        <option value="3" @if(isset($query)&&$query['details_type']==3) selected @endif>SaaS订单</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <select id="status" class="form-control"  name="type" tabindex="1">
                                        <option value="">订单来源</option>
                                        <option value="1" @if(isset($query)&&$query['type']==1) selected @endif>后台创建</option>
                                        <option value="2" @if(isset($query)&&$query['type']==2) selected @endif>用户购买</option>
                                    </select>
                                </div>
                            </div>


                            <div class="input-group-btn" style="display: inline-block;width: 150px;">
                                <input type="text" name="shelf_at" class="form-control"
                                       style="display: inline-block;width: 160px;" id="shelf_at" placeholder="创建时间--开始"
                                       value="@if(isset($query)){{$query['shelf_at']}}@endif"/>
                            </div>
                            <div class="input-group-btn" style="display: inline-block;width: 150px;">
                                <input type="text" name="endshelf_at" class="form-control"
                                       style="display: inline-block;width: 160px;" id="endshelf_at" placeholder="创建时间--结束"
                                       value="@if(isset($query)){{$query['endshelf_at']}}@endif"/>
                            </div>

                            <div class="input-group-btn" style="display: inline-block;width: 150px;">
                                <input type="text" name="pay_at" class="form-control"
                                       style="display: inline-block;width: 160px;" id="pay_at" placeholder="支付时间--开始"
                                       value="@if(isset($query)){{$query['pay_at']}}@endif"/>
                            </div>
                            <div class="input-group-btn" style="display: inline-block;width: 150px;">
                                <input type="text" name="endpay_at" class="form-control"
                                       style="display: inline-block;width: 160px;" id="endpay_at" placeholder="支付时间--结束"
                                       value="@if(isset($query)){{$query['endpay_at']}}@endif"/>
                            </div>

                            <span class="input-group-btn" style="display: inline-block;">
                                                    <button type="submit" class="btn btn-purple btn-sm"
                                                            style="margin-left: 20px;">
                                                        <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                                        搜索
                                                    </button>
                                                </span>
                        </div>
                    </form>
                </div>

                <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;text-align: center">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 5%">ID</th>
                        <th class="text-center" style="width: 9%">订单编号</th>
                        <th class="text-center" style="width: 9%">用户账号</th>
                        <th class="text-center" style="width: 9%">支付方式</th>
                        <th class="text-center" style="width: 8%">订单金额</th>
                        <th class="text-center" style="width: 6%">订单状态</th>
                        <th class="text-center" style="width: 6%">订单来源</th>
                        <th class="text-center" style="width: 6%">订单类型</th>
                        <th class="text-center" style="width: 11%">创建时间</th>
                        <th class="text-center" style="width: 11%">支付时间</th>
                        <th class="text-center" style="width: 10%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr id="del_{{$item['id']}}">
                            <td class="text-center">{{$item['id']}}</td>
                            <td class="text-center">{{$item['order_no']}}</td>
                            <td>{{$item['email']}}</td>
                            <td>
                                    @if($item['pay_type'] == 1)
                                            <span class="ladda-label">paddle支付</span>
                                    @elseif($item['pay_type'] == 2)
                                            <span class="ladda-label">支付宝</span>
                                    @elseif($item['pay_type'] == 3)
                                            <span class="ladda-label">微信</span>
                                    @else
                                            <span class="ladda-label">未支付</span>
                                    @endif

                            </td>
                            <td>{{$item['price']}}</td>
                            <td>
                                @if($item['status'] == 1)
                                    <span class="ladda-label">已付款</span>
                                @elseif($item['status'] == 2)
                                    <span class="ladda-label">已完成</span>
                                @elseif($item['status'] == 3)
                                    <span class="ladda-label">待退款</span>
                                @elseif($item['status'] == 4)
                                    <span class="ladda-label">已关闭</span>
                                @else
                                    <span class="ladda-label">待支付</span>
                                @endif

                            </td>
                            <td>
                                @if($item['type'] == 1)
                                    <span class="ladda-label">后台创建</span>
                                @elseif($item['type'] == 2)
                                    <span class="ladda-label">用户购买</span>
                                @else
                                    <span class="ladda-label">未知</span>
                                @endif

                            </td>
                            <td>
                                @if($item['details_type'] == 1)
                                    <span class="ladda-label">SDK试用</span>
                                @elseif($item['details_type'] == 2)
                                    <span class="ladda-label">SDK订单</span>
                                @elseif($item['details_type'] == 3)
                                    <span class="ladda-label">SaaS订单</span>
                                @else
                                    <span class="ladda-label">未知</span>
                                @endif

                            </td>
                            <td>{{$item['created_at']}}</td>
                            <td>{{$item['pay_time']}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a class="btn  btn-xs" style="text-decoration: none;color: #f6fff8;background: #0b94ea" title="预览 " href="{{route('order.getinfo',$item['id'])}}">
                                        <i class="fa fa-users"></i> 详情
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$data->appends(['info' => isset($query['info'])?$query['info']:'','query_type'=>isset($query['query_type'])?$query['query_type']:'','status'=>isset($query['status'])?$query['status']:'','start_date'=>isset($query['start_date'])?$query['start_date']:'','end_date'=>isset($query['end_date'])?$query['end_date']:'','level1'=>isset($query['level1'])?$query['level1']:'','level2'=>isset($query['level2'])?$query['level2']:'','level3'=>isset($query['level3'])?$query['level3']:''])->links()}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script>
        var id = 0;
        var indexs;


        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //执行一个laydate实例
            var start = laydate.render({
                elem: '#shelf_at', //指定元素
                max: 1,//最大值为当前日期
                trigger: 'click',
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-30),//默认值30天前
                done: function (value, date) {
                    if (value && (value > $("#endshelf_at").val())) {
                        /*开始时间大于结束时间时，清空结束时间*/
                        $("#endshelf_at").val("");
                    }
                    end.config.min = {
                        year: date.year,
                        month: date.month - 1,
                        date: date.date,
                        hours: date.hours,//可注释
                        minutes: date.minutes,//可注释
                        seconds: date.seconds//可注释
                    };
                }
            });
            var end = laydate.render({
                elem: '#endshelf_at', //指定元素
                max: 1,//最大值为当前日期
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-1),//默认值昨天
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });


        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //执行一个laydate实例
            var start = laydate.render({
                elem: '#pay_at', //指定元素
                max: 1,//最大值为当前日期
                trigger: 'click',
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-30),//默认值30天前
                done: function (value, date) {
                    if (value && (value > $("#endpay_at").val())) {
                        /*开始时间大于结束时间时，清空结束时间*/
                        $("#endpay_at").val("");
                    }
                    end.config.min = {
                        year: date.year,
                        month: date.month - 1,
                        date: date.date,
                        hours: date.hours,//可注释
                        minutes: date.minutes,//可注释
                        seconds: date.seconds//可注释
                    };
                }
            });
            var end = laydate.render({
                elem: '#endpay_at', //指定元素
                max: 1,//最大值为当前日期
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-1),//默认值昨天
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });



        $(function () {

            //导出
            $("#export").click(function () {
                html =  '<div style="display: flex; justify-content: left;flex-wrap: wrap; padding: 10px">' +
                    '<div style="margin-bottom: 20px"><label style="margin-right: 10px; width: 50px"><input name="id"  type="checkbox"  value="id" checked="checked"/>ID</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="products"  type="checkbox"  value="level1" checked="checked"/>Products</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="platform"  type="checkbox"  value="level2" checked="checked"/>Platform</label>' +
                    '<label style="margin-right: 10px; width: 120px"><input name="licensie"  type="checkbox"  value="level3" checked="checked"/>Licensie Type</label>' +
                    '<label style="margin-right: 10px; width: 120px"><input name="price"  type="checkbox"  value="price" checked="checked"/>Pricing(USD)</label></div>' +
                    '<div><label style="margin-right: 10px; width: 50px"><input name="status"  type="checkbox"  value="status" checked="checked"/>状态</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="created_at"  type="checkbox"  value="created_at" checked="checked"/>创建时间</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="updated_at"  type="checkbox"  value="updated_at" checked="checked"/>更新时间</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="shelf_at"  type="checkbox"  value="shelf_at" checked="checked"/>上架时间</label></div></div>';

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

                        if(field.length == 0){
                            alert("至少需要一列导出字段")
                            return false;
                        }

                        let query_type =  $('#query_type').find("option:selected").val()
                        let info = $('#info').val()
                        let level1 = $('#province').val()
                        let level2 = $('#city').val()
                        let level3 = $('#town').val()

                        let status = $('#status').find("option:selected").val()
                        let startDate = $('#startDate').val()
                        let endDate = $('#endDate').val()

                        $.ajax({
                            url: "{{route('goods.index')}}",
                            header: {
                                contentType: "application/octet-stream"
                            },
                            data: "query_type="+ query_type + "info=" + info + "&level1=" + level1 + "&level2=" + level2 + "&level3=" + level3 + "&status=" + status + "&start_date=" + startDate + "&end_date=" + endDate
                                + "&field=" + field.join(',') + "&export=1",
                            type: 'get',
                            success: function (res) {
                                //导出
                                location.href = res.url;
                            }
                        });
                    }
                });
            });
        })
    </script>
@endsection