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
</style>
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<link rel="stylesheet" href="/layui/css/layui.css" media="all">
<script src="{{loadEdition('/layui/layui.js')}}"></script>
<textarea style="display: none" id="lv1">{{$lv1}}</textarea>
<textarea style="display: none" id="lv2">{{$lv2}}</textarea>
<textarea style="display: none" id="lv3">{{$lv3}}</textarea>
<input type="hidden" id="ls1" value="{{$query['level1']}}">
<input type="hidden" id="ls2" value="{{$query['level2']}}">
<input type="hidden" id="ls3" value="{{$query['level3']}}">
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>授权码</h5>
            <button id="export" class="btn layui-btn-primary btn-sm" type="button"
                    style="float: right;margin-left: 5px"><i class="fa fa-paste"></i>导出数据
            </button>
            <a style="float: right;margin-left: 5px" href="{{route('license.createLicense')}}"
               link-url="javascript:void(0)">
                <button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 授权码
                </button>
            </a>
        </div>
        <div class="ibox-content">

            <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px">
                <form name="admin_list_sea" class="form-search" method="get" action="{{route('license.index')}}">
                    <div class="input-group" style="margin-left: 0 auto">
                        <div class="input-group-btn" style="vertical-align: top;">
                            <select name="query_type" class="form-control" style="display: inline-block;width: 115px;" id="query_type">
                                <option value="order_no" @if(isset($query)&&$query['query_type']=='order_no') selected @endif>订单编号
                                </option>
                                <option value="goods_no" @if(isset($query)&&$query['query_type']=='order_no') selected @endif>子单编号
                                </option>
                                <option value="uuid" @if(isset($query)&&$query['query_type']=='uuid') selected @endif>App ID/Machine ID
                                </option>
                                <option value="email" @if(isset($query)&&$query['query_type']=='email') selected @endif>用户账号
                                </option>
                            </select>
                        </div>
                        <input type="text" name="info" id="info" class="form-control" style="display: inline-block;width: 200px;" value="@if(isset($query)){{$query['info']}}@endif"/>
                        <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <select id="type" class="form-control" name="type" tabindex="1">
                                    <option value="">请选择授权码类型</option>
                                    @foreach($license_type as $key => $value)
                                        <option value="{{$key}}" @if(isset($query)&&$query['type']==$key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                            <select name="level1" id="province" class="form-control"></select>
                        </div>
                        <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                            <select name="level2" id="city" class="form-control"></select>
                        </div>
                        <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                            <select name="level3" id="town" class="form-control"></select>
                        </div>

                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                            <input type="text" name="created_start" class="form-control"
                                   style="display: inline-block;width: 160px;" id="created_start" placeholder="创建时间-开始"
                                   value="@if(isset($query)){{$query['created_start']}}@endif"/>
                        </div>
                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                            <input type="text" id="created_end" name="created_end" class="form-control"
                                   style="display: inline-block;width: 160px;" placeholder="创建时间-结束"
                                   value="@if(isset($query)){{$query['created_end']}}@endif"/>
                        </div>

                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                            <input type="text" name="expire_start" class="form-control"
                                   style="display: inline-block;width: 160px;" id="expire_start" placeholder="过期时间-开始"
                                   value="@if(isset($query)){{$query['expire_start']}}@endif"/>
                        </div>
                        <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                            <input type="text" name="expire_end" class="form-control"
                                   style="display: inline-block;width: 160px;" id="expire_end" placeholder="过期时间-结束"
                                   value="@if(isset($query)){{$query['expire_end']}}@endif"/>
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

            <table class="table table-striped table-bordered table-hover m-t-md"
                   style="word-wrap:break-word; word-break:break-all;">
                <thead>
                <tr>
                    <th class="text-center" style="width: 9%">总订单号</th>
                    <th class="text-center" style="width: 9%">子订单号</th>
                    <th class="text-center" style="width: 9%">用户账号</th>
                    <th class="text-center" style="width: 7%">商品名称</th>
                    <th class="text-center" style="width: 8%">App ID/Machine ID</th>
                    <th class="text-center" style="width: 9%">创建时间</th>
                    <th class="text-center" style="width: 8%">过期时间</th>

                    <th class="text-center" style="width: 7%">license_key</th>
                    <th class="text-center" style="width: 5%">授权码类型</th>
                    <th class="text-center" style="width: 3%">状态</th>
                    <th class="text-center" style="width: 10%">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $item)
                    <tr>
                        <td class="text-center">{{$item->order_id}}</td>
                        <td class="text-center">{{$item->order_no}}</td>
                        @if($item->lise_type==1)
                         <td class="text-center" title="{{$item->user_email}}">{{$item->user_email}}</td>
                        @else
                        <td class="text-center" title="{{$item->email}}">{{$item->emaild}}</td>
                        @endif
                        <td class="text-center" title="{{$item->name}}">{{$item->named}}</td>
                        <td class="text-center" title="{{$item->uuid}}">{{$item->uuidd}}</td>
                        <td class="text-center">{{$item->created_at}}</td>
                        <td class="text-center">{{$item->expire_time}}</td>

                        <td class="text-center" title="{{$item->license_key}}"><?php echo substr($item->license_key,0,10);?></td>
                        <td class="text-center">{{$item->type}}</td>
                        <td class="text-center">{{$item->statusd}}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{route('license.info',$item->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>查看</button></a>
{{--                                @if($item->status == 1)--}}
{{--                                    <a onclick="changeStatus('{{$item->id}}',2)"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i>停用</button></a>--}}
{{--                                @else--}}
{{--                                    <a onclick="changeStatus('{{$item->id}}',1)"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i>启用</button></a>--}}
{{--                                @endif--}}
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$data->appends([
                'info' => isset($query['info'])?$query['info']:'',
                'query_type'=>isset($query['query_type'])?$query['query_type']:'',
                'type'=>isset($query['type'])?$query['type']:'',
                'created_start'=>isset($query['created_start'])?$query['created_start']:'',
                'created_end'=>isset($query['created_end'])?$query['created_end']:'',
                'expire_start'=>isset($query['expire_start'])?$query['expire_start']:'',
                'expire_end'=>isset($query['expire_end'])?$query['expire_end']:''])
                ->links()}}
        </div>
    </div>
    <div class="clearfix">
    </div>
</div>

<script>
    var id = 0;
    var indexs;
    function changeStatus(id,status) {
        var str = status == 1 ? '启用' : '停用';
        layer.confirm('您确定要执行'+str+'吗？', {
            btn: ['确定', '取消']
        }, function () {
            let index = layer.load();
            $.ajax({
                url: "{{route('license.changeStatus')}}",
                data: {id: id, status: status, _token: '{{ csrf_token() }}'},
                type: 'post',
                success: function (resp) {
                    layer.close(index);
                    if (resp.code == 200) {
                        layer.msg("操作成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            location.reload();
                        });
                    }
                }
            });
        }, function (index) {
            layer.close(index);
        });
    }

    layui.use('laydate', function () {
        var laydate = layui.laydate;

        //执行一个laydate实例
        var start = laydate.render({
            elem: '#created_start', //指定元素
            max: 1,//最大值为当前日期
            trigger: 'click',
            type: 'datetime',//日期时间选择器
            // value: getRecentDay(-30),//默认值30天前
            done: function (value, date) {
                if (value && (value > $("#created_end").val())) {
                    /*开始时间大于结束时间时，清空结束时间*/
                    $("#created_end").val("");
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
            elem: '#created_end', //指定元素
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
            elem: '#expire_start', //指定元素
            max: 3,//最大值为当前日期
            trigger: 'click',
            type: 'datetime',//日期时间选择器
            // value: getRecentDay(-30),//默认值30天前
            done: function (value, date) {
                if (value && (value > $("#expire_end").val())) {
                    /*开始时间大于结束时间时，清空结束时间*/
                    $("#expire_end").val("");
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
            elem: '#expire_end', //指定元素
            max: '2099-12-31',//最大值为当前日期
            type: 'datetime',//日期时间选择器
            // value: getRecentDay(-1),//默认值昨天

            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        });
    });


    function show(id, status) {
        if (status == 1) {
            var ale = "你确定下架此商品吗？"
        } else {
            var ale = "你确定上架此商品吗？"
        }
        layer.confirm(ale, {
            btn: ['确定', '取消']
        }, function () {
            let index = layer.index;
            $.ajax({
                url: "{{route('goods.show')}}",
                data: {id: id, _token: '{{ csrf_token() }}'},
                type: 'post',
                //dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    layer.close(index);
                    if (resp.code == 0) {
                        if (resp.status == 0) {
                            var htmls = '<a type="button" style="text-decoration: none;color: #f6fff8"   data-id="{$v.id}"  class="openBtn_' + id + ' abutton cloros" data-style="zoom-out" onclick="show(' + id + ',' + resp.status + ');" title="当前下架状态"> <span class="ladda-label">上架</span></a>';
                        } else {
                            var htmls = '<a type="button" style="text-decoration: none;color: #f6fff8"  data-id="{$v.id}"  class="openBtn_' + id + ' abutton cloros1" data-style="zoom-out" onclick="show(' + id + ',' + resp.status + ');" title="当前上架状态"> <span class="ladda-label">下架</span></a>';
                        }
                        $(".open_" + id).html(htmls);
                    } else {
                        //失败提示
                        layer.msg(resp.msg, {
                            icon: 2,
                            time: 2000
                        });
                    }
                }, error: function (response) {
                    layer.msg("请检查网络或权限设置！", {
                        icon: 2,
                        time: 2000
                    });
                    layer.close(index);
                }
            });
        }, function (index) {
            layer.close(index);
        });
    }

    $(function () {
        var proarr = JSON.parse($("#lv1").text());
        var ciarr = JSON.parse($("#lv2").text());
        var toarr = JSON.parse($("#lv3").text());
        var level1 = $("#ls1").val();
        var level2 = $("#ls2").val();
        var level3 = $("#ls3").val();
        //遍历省份数组，将省份添加到省份下拉列表中
        console.log(toarr)
        $.each(proarr, function () {
            if(this.id==level1){
                $("#province").append("<option value='"+this.id+"' selected>" + this.title + "</option>>")
            }else{
                $("#province").append("<option value='"+this.id+"'>" + this.title + "</option>>")
            }

        })
        var index = $("#province option:checked").index();

        $.each(ciarr[index], function () {
            if(this.id==level2){
                $("#city").append("<option value='"+this.id+"' selected>" + this.title + "</option>>")
            }else {
                $("#city").append("<option value='" + this.id + "'>" + this.title + "</option>>")
            }
        })

        var index1 = $("#province option:checked").index();
        //获取被点击的城市的索引
        var index2 = $("#city option:checked").index();
        $.each(toarr[index1][index2], function () {
            if(this.id==level3){
                $("#town").append("<option value='"+this.id+"' selected>" + this.title + "</option>>");
            }else{
                $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");
            }
        })

        //创建一个用户改变域的内容的事件：改变省份下拉列表中的内容
        $("#province").change(function () {

            //获取被点击的省份的索引
            var index = $("#province option:checked").index();

            //先清空城市下拉列表中的内容
            $("#city").empty();

            //根据获得的省份索引，遍历城市数组中对应的索引中的内容，将内容添加到城市下拉列表中

            $.each(ciarr[index], function () {
                $("#city").append("<option value='"+this.id+"'>" + this.title + "</option>>")
            })
            var index1 = $("#province option:checked").index();
            //获取被点击的城市的索引
            var index2 = $("#city option:checked").index();

            //清空县区下拉列表中的内容
            $("#town").empty();

            //根据被点击的省份和城市索引，遍历县区数组中对应的索引中的内容，将内容添加到县区下拉列表中去
            $.each(toarr[index1][index2], function () {
                $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");
            })
        })

        //创建一个用户改变域的内容的事件：改变城市下拉列表中的内容
        $("#city").change(function () {

            //获得被点击的省份的索引
            var index1 = $("#province option:checked").index();
            //获取被点击的城市的索引
            var index2 = $("#city option:checked").index();

            //清空县区下拉列表中的内容
            $("#town").empty();

            //根据被点击的省份和城市索引，遍历县区数组中对应的索引中的内容，将内容添加到县区下拉列表中去
            $.each(toarr[index1][index2], function () {
                $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");
            })
        })

        //导出
        html =  '<div style="display: flex; justify-content: left;flex-wrap: wrap; padding: 10px">' +
            '<div style="margin-bottom: 10px"><label style="margin-right: 10px; width: 100px"><input name="order_id"  type="checkbox"  value="order_id" checked="checked"/>总订单编号</label>' +
            '<label style="margin-right: 10px; width: 100px"><input name="order_no"  type="checkbox"  value="order_no" checked="checked"/>子订单号</label>' +
            '<label style="margin-right: 10px; width: 100px"><input name="email"  type="checkbox"  value="email" checked="checked"/>用户账号</label>' +
            '<label style="margin-right: 10px; width: 120px"><input name="name"  type="checkbox"  value="name" checked="checked"/>商品名称</label></div>' +

            '<div style="margin-bottom: 10px"><label style="margin-right: 10px; width: 150px"><input name="uuid"  type="checkbox"  value="uuid" checked="checked"/>App ID/Machine ID</label>' +
            '<label style="margin-right: 10px; width: 100px"><input name="created_at"  type="checkbox"  value="created_at" checked="checked"/>创建时间</label>' +
            '<label style="margin-right: 10px; width: 100px"><input name="expire_time"  type="checkbox"  value="expire_time" checked="checked"/>过期时间</label>' +
            '<label style="margin-right: 10px; width: 100px"><input name="license_key"  type="checkbox"  value="license_key" checked="checked"/>license_key</label></div>' +

            '<div><label style="margin-right: 10px; width: 100px"><input name="type"  type="checkbox"  value="type" checked="checked"/>授权码类型</label>' +
            '<label style="margin-right: 10px; width: 100px"><input name="status"  type="checkbox"  value="status" checked="checked"/>状态</label></div></div>';

        $("#export").click(function () {

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
                    let type =  $('#type').find("option:selected").val()
                    let info = $('#info').val()
                    let level1 = $('#province').val()
                    let level2 = $('#city').val()
                    let level3 = $('#town').val()

                    let created_start = $('#created_start').val()
                    let created_end = $('#created_end').val()
                    let expire_start = $('#expire_start').val()
                    let expire_end = $('#expire_end').val()

                    location.href ="/admin/license/index?query_type="+ query_type + "&info=" + info + "&level1=" + level1 + "&level2=" + level2 + "&level3=" + level3 + "&type=" + type + "&created_start=" + created_start + "&created_end=" + created_end+ "&expire_start=" + expire_start+ "&expire_end=" + expire_end
                    + "&field=" + field.join(',') + "&export=1";
                }
            });
        });
    })
</script>
@endsection