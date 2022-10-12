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

    <textarea style="display: none" id="lv1">{{$lv1}}</textarea>
    <textarea style="display: none" id="lv2">{{$lv2}}</textarea>
    <textarea style="display: none" id="lv3">{{$lv3}}</textarea>
    <input type="hidden" id="ls1" value="{{$query['level1']}}">
    <input type="hidden" id="ls2" value="{{$query['level2']}}">
    <input type="hidden" id="ls3" value="{{$query['level3']}}">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Goods</h5>
                <button id="export" class="btn layui-btn-primary btn-sm" type="button" style="float: right;margin-left: 5px"><i class="fa fa-paste"></i>导出数据</button>
                <a style="float: right;margin-left: 5px" href="{{route('goods.creategoods')}}" link-url="javascript:void(0)">
                    <button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Goods</button>
                </a>
            </div>
            <div class="ibox-content">

                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('goods.index')}}">
                        <div class="input-group">

                            <div class="input-group-btn" style="display: block;">
                                <select id="query_type" name="query_type" class="form-control"
                                        style="display: inline-block;width: 100px;">
                                    <option value="id" @if(isset($query)&&$query['query_type']=='id') selected @endif>
                                        ID
                                    </option>
                                </select>
                            </div>
                            <input id="info" type="text" name="info" class="form-control" style="display: inline-block;width: 150px;
                                   value="@if(isset($query)){{$query['info']}}@endif"/>

                            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                                <select name="level1" id="province" class="form-control"></select>
                            </div>
                            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                                <select name="level2" id="city" class="form-control"></select>
                            </div>
                            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                                <select name="level3" id="town" class="form-control"></select>
                            </div>
                            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <select id="status" class="form-control"  name="status" tabindex="1">
                                        <option value="">筛选状态</option>
                                        <option value="2" @if(isset($query)&&$query['status']==2) selected @endif>上架</option>
                                        <option value="1" @if(isset($query)&&$query['status']==1) selected @endif>下架</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group-btn" style="display: inline-block;width: 150px;">
                                <input type="text" name="start_date" class="form-control"
                                       style="display: inline-block;width: 160px;" id="startDate" placeholder="创建时间--开始"
                                       value="@if(isset($query)){{$query['start_date']}}@endif"/>
                            </div>
                            <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                                <input type="text" name="end_date" class="form-control"
                                       style="display: inline-block;width: 160px;" id="endDate" placeholder="创建时间--结束"
                                       value="@if(isset($query)){{$query['end_date']}}@endif"/>
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

                <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 5%">ID</th>
                        <th class="text-center" style="width: 9%">Products</th>
                        <th class="text-center" style="width: 9%">Platform</th>
                        <th class="text-center" style="width: 9%">Licensie Type</th>
                        <th class="text-center" style="width: 8%">Pricing(USD)</th>
                        <th class="text-center" style="width: 6%">状态</th>
                        <th class="text-center" style="width: 11%">创建时间</th>
                        <th class="text-center" style="width: 11%">更新时间</th>
                        <th class="text-center" style="width: 11%">上架时间</th>
                        <th class="text-center" style="width: 10%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr id="del_{{$item['id']}}">
                            <td class="text-center">{{$item['id']}}</td>
                            <td class="text-center">{{$item['products']}}</td>
                            <td>{{$item['platform']}}</td>
                            <td>{{$item['licensie']}}</td>
                            <td>{{$item['price']}}</td>
                            <td>
                                <font class="open_{{$item['id']}}">
                                    @if($item['status'] == 1)
                                        <a data-id="{{$item['id']}}" class="openBtn_{{$item['id']}} abutton cloros1"
                                           data-style="zoom-out" onclick="show({{$item['id']}},{{$item['status']}});" title="当前上架状态">
                                            <span class="ladda-label">下架</span>
                                        </a>
                                    @else
                                        <a data-id="{{$item['id']}}" class="openBtn_{{$item['id']}} abutton cloros"
                                           data-style="zoom-out" onclick="show({{$item['id']}},{{$item['status']}});" title="当前下架状态">
                                            <span class="ladda-label">上架</span>
                                        </a>
                                    @endif
                                </font>
                            </td>
                            <td>{{$item['created_at']}}</td>
                            <td>{{$item['updated_at']}}</td>
                            <td>{{$item['shelf_at']}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a class="btn  btn-xs" style="text-decoration: none;color: #f6fff8;background: #0b94ea" title="预览 " href="{{route('goods.info',$item['id'])}}">
                                        <i class="fa fa-users"></i> 预览
                                    </a>
                                    <a id="update_{{$item['id']}}" href="{{route('goods.updategoods',$item['id'])}}">
                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>
                                            修改
                                        </button>
                                    </a>
                                    <a onclick="del('{{$item['id']}}')">
                                        <button class="btn btn-danger del btn-xs" type="button"><i
                                                    class="fa fa-trash-o"></i> 删除
                                        </button>
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

        function del(id) {
            layer.confirm('您确定要删除吗？', {
                btn: ['确定', '取消']
            }, function () {
                // layer.close(index);
                let index = layer.load();
                $.ajax({
                    url: "{{route('goods.delgoods')}}",
                    data: {delid: id, _token: '{{ csrf_token() }}'},
                    type: 'post',
                    // dataType: "json",
                    success: function (resp) {
                        layer.close(index);
                        //成功提示
                        if (resp.code == 0) {
                            layer.msg("删除成功", {
                                icon: 1,
                                time: 1000
                            }, function () {
                                $("#del_" + id).remove();
                            });
                        } else {
                            //失败提示
                            if (resp.msg) {
                                layer.msg(resp.msg, {
                                    icon: 2,
                                    time: 2000
                                });
                            } else {
                                layer.msg("请检查网络或权限设置！！！", {
                                    icon: 2,
                                    time: 2000
                                });
                            }
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
                elem: '#startDate', //指定元素
                max: 1,//最大值为当前日期
                trigger: 'click',
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-30),//默认值30天前
                done: function (value, date) {
                    if (value && (value > $("#endDate").val())) {
                        /*开始时间大于结束时间时，清空结束时间*/
                        $("#endDate").val("");
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
                elem: '#endDate', //指定元素
                max: 1,//最大值为当前日期
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-1),//默认值昨天
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });


        function show(id,status) {

            if(status==1){
                var ale="你确定下架此商品吗？"
            }else{
                var ale="你确定上架此商品吗？"
            }
            layer.confirm(ale, {
                btn: ['确定', '取消']
            },function () {
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
                                var htmls = '<a type="button" style="text-decoration: none;color: #f6fff8"   data-id="{$v.id}"  class="openBtn_' + id + ' abutton cloros" data-style="zoom-out" onclick="show(' + id + ','+resp.status+');" title="当前下架状态"> <span class="ladda-label">上架</span></a>';
                            } else {
                                var htmls = '<a type="button" style="text-decoration: none;color: #f6fff8"  data-id="{$v.id}"  class="openBtn_' + id + ' abutton cloros1" data-style="zoom-out" onclick="show(' + id + ','+resp.status+');" title="当前上架状态"> <span class="ladda-label">下架</span></a>';
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
                    if (this.id > 0) {
                        $("#city").append("<option value='" + this.id + "'>" + this.title + "</option>>")
                    }
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