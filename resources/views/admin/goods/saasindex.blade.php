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
        .ab{
            display: inline-block;
            width: 75px;
            height: 35px;
            margin-left: 15px;
            line-height: 35px;
            color: #0b94ea;
            text-decoration: none;
            border: 1px solid royalblue;
            text-align: center;
        }
    </style>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>

    <textarea style="display: none" id="lv1">{{$lv1}}</textarea>
    <textarea style="display: none" id="lv2">{{$lv2}}</textarea>
    <input type="hidden" id="ls1" value="{{$query['level1']}}">
    <input type="hidden" id="ls2" value="{{$query['level2']}}">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商品列表</h5>
                <div style="float: right; width: 300px">
                    <a style="margin-left: 2%" href="{{route('goods.createsaasgoods')}}" link-url="javascript:void(0)">
                        <button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 新增商品</button>
                    </a>
                    <button id="previews" class="btn layui-btn-primary btn-sm" type="button" style="margin-left: 2%;">页面预览</button>
                    <button id="export" class="btn layui-btn-primary btn-sm" type="button" style="margin-left: 2%">导出数据</button>
                </div>
            </div>
            <div style="width: 100%;padding-bottom: 15px;padding-top: 10px;background: #fbfffa">
                <a class="ab"  href="{{route('goods.index')}}">SDK</a>
                <a class="ab" style="background: #b4b7b3" href="{{route('goods.saasIndex')}}">API</a>
            </div>
            <div class="ibox-content">

                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px; padding: 0;">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('goods.saasIndex')}}">
                        <div class="input-group" style="display: block;height: 34px">
{{--                            <div class="input-group-btn" style="display: block;">--}}
{{--                                <select id="query_type" name="query_type" class="form-control"--}}
{{--                                        style="display: inline-block;width: 100px;">--}}
{{--                                    <option value="sort_num" @if(isset($query)&&$query['query_type']=='sort_num') selected @endif>--}}
{{--                                        序号--}}
{{--                                    </option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
                            <input id="info" type="text" name="info" placeholder="请输入序号" class="form-control" style="display: inline-block;width: 10%;"
                                   value="@if(isset($query)){{$query['info']}}@endif"/>

                            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12" style="width: 20%;">
                                <select name="level1" id="province" class="form-control"></select>
                            </div>
                            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12" style="width: 20%;">
                                <select name="level2" id="city" class="form-control"></select>
                            </div>
                            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12" style="width: 20%;">
                                <div class="form-group">
                                    <select id="status" class="form-control"  name="status" tabindex="1">
                                        <option value="">商品状态</option>
                                        <option value="1" @if(isset($query)&&$query['status'] == 1) selected @endif>架上商品</option>
                                        <option value="0" @if(isset($query)&&$query['status'] === "0") selected @endif>待上架</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="input-group" style="display: block;height: 34px; margin-top: 5px">

                            <div class="input-group-btn" style="display: inline-block;width: 15%;">
                                <input type="text" name="created_at" class="form-control"
                                       style="display: inline-block;" id="created_at" placeholder="创建时间"
                                       value="@if(isset($query)){{$query['created_at']}}@endif"/>
                            </div>

                            <div class="input-group-btn" style="display: inline-block;width: 15%; margin-left: 12px">
                                <input type="text" name="updated_at" class="form-control"
                                       style="display: inline-block;" id="updated_at" placeholder="更新时间"
                                       value="@if(isset($query)){{$query['updated_at']}}@endif"/>
                            </div>

                            <div class="input-group-btn" style="display: inline-block;width: 15%; margin-left: 12px">
                                <input type="text" name="shelf_at" class="form-control"
                                       style="display: inline-block;" id="shelf_at" placeholder="上架时间"
                                       value="@if(isset($query)){{$query['shelf_at']}}@endif"/>
                            </div>

                            <span class="input-group-btn" style="display: inline-block;">
                                <button type="submit" class="btn btn-purple btn-sm" style="margin-left: 20px;">
                                    <span class="ace-icon fa fa-search icon-on-right bigger-110">搜索</span>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>

                <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 5%">序号</th>
                        <th class="text-center" style="width: 9%">套餐类型</th>
                        <th class="text-center" style="width: 9%">档位（资产数）</th>
                        <th class="text-center" style="width: 8%">价格（$）</th>
                        <th class="text-center" style="width: 6%">商品状态</th>
                        <th class="text-center" style="width: 11%">创建时间</th>
                        <th class="text-center" style="width: 11%">更新时间</th>
                        <th class="text-center" style="width: 11%">上架时间</th>
                        <th class="text-center" style="width: 15%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr id="del_{{$item['id']}}">
                            <td class="text-center">{{$item['sort_num']}}</td>
                            <td class="text-center">{{$item['products']}}</td>
                            <td>{{$item['platform']}}</td>
                            <td>{{$item['price']}}</td>
                            <td id="status_{{$item['id']}}">
                                    @if($item['status'] == 1)
                                            <span class="ladda-label">架上商品</span>
                                    @else
                                            <span class="ladda-label">待上架</span>
                                    @endif
                            </td>
                            <td>{{$item['created_at']}}</td>
                            <td>{{$item['updated_at']}}</td>
                            <td>{{$item['shelf_at']}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a class="btn  btn-xs" href="{{route('goods.saasinfo',$item['id'])}}">
                                        <button class="btn btn-primary layui-btn-xs layui-btn-normal" type="button">
                                            <i class="fa fa-users"></i> 预览
                                        </button>
                                    </a>
                                    <a id="update_{{$item['id']}}" href="{{route('goods.updatesaasgoods',$item['id'])}}">
                                        <button class="btn btn-primary btn-xs" type="button">
                                            <i class="fa fa-paste"></i> 修改
                                        </button>
                                    </a>
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
                                    <a onclick="del('{{$item['id']}}')">
                                        <button class="btn btn-danger del btn-xs" type="button">
                                            <i class="fa fa-trash-o"></i> 删除
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$data->appends(['info' => isset($query['info'])?$query['info']:'','query_type'=>isset($query['query_type'])?$query['query_type']:'','status'=>isset($query['status'])?$query['status']:'','start_date'=>isset($query['start_date'])?$query['start_date']:'','end_date'=>isset($query['end_date'])?$query['end_date']:'','level1'=>isset($query['level1'])?$query['level1']:'','level2'=>isset($query['level2'])?$query['level2']:''])->links()}}
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

            laydate.render({
                elem: '#created_at', //指定元素
                max: 0 ,//最大值为当前日期
                trigger: 'click',
                type: 'date',//日期时间选择器
                range:'/'
            });

            laydate.render({
                elem: '#updated_at', //指定元素
                max:0,
                trigger: 'click',
                type: 'date',//日期时间选择器
                range:'/'
            });

            laydate.render({
                elem: '#shelf_at', //指定元素
                max:0,
                trigger: 'click',
                type: 'date',//日期时间选择器
                range:'/'
            });
        });

        function show(id,status) {

            if(status==1){
                var ale="商品下架后将不在官网进行展示，你确定下架此商品吗？"
            }else{
                var ale="商品上架后将在官网进行展示，你确定要上架此商品吗？"
            }
            layer.confirm(ale, {
                btn: ['确定', '取消']
            },function () {
                let index = layer.index;
                layer.close(index);
                var indexLoad = layer.load();
                $.ajax({
                    url: "{{route('goods.show')}}",
                    data: {id: id, _token: '{{ csrf_token() }}'},
                    type: 'post',
                    //dataType: "json",
                    success: function (resp) {
                        // if (resp.code == 0) {
                        //     if (resp.status == 0) {
                        //         var htmls = '<a type="button" style="text-decoration: none;color: #f6fff8"   data-id="{$v.id}"  class="openBtn_' + id + ' abutton cloros" data-style="zoom-out" onclick="show(' + id + ','+resp.status+');" title="当前下架状态"> <span class="ladda-label">上架</span></a>';
                        //         $("#status_"+id).html('<span class="ladda-label">待上架</span>');
                        //     } else {
                        //         var htmls = '<a type="button" style="text-decoration: none;color: #f6fff8"  data-id="{$v.id}"  class="openBtn_' + id + ' abutton cloros1" data-style="zoom-out" onclick="show(' + id + ','+resp.status+');" title="当前上架状态"> <span class="ladda-label">下架</span></a>';
                        //         $("#status_"+id).html('<span class="ladda-label">架上商品</span>');
                        //     }
                        //
                        //     $(".open_" + id).html(htmls);
                        //
                        // } else {
                        //     //失败提示
                        //     layer.msg(resp.msg, {
                        //         icon: 2,
                        //         time: 2000
                        //     });
                        // };
                        layer.close(indexLoad);
                        location.href = "{{route('goods.saasIndex')}}";
                    },
                    error: function (response) {
                        layer.close(indexLoad);
                        layer.msg("请检查网络或权限设置！", {
                            icon: 2,
                            time: 2000
                        });
                        location.href = "{{route('goods.saasIndex')}}";
                    }
                });
            }, function (index) {
                layer.close(index);
            });
        }


        $(function () {
            var proarr = JSON.parse($("#lv1").text());
            var ciarr = JSON.parse($("#lv2").text());
            var level1 = $("#ls1").val();
            var level2 = $("#ls2").val();
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
                        $("#city").append("<option value='" + this.id + "'>" + this.title + "</option>>")
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

            })

            //创建一个用户改变域的内容的事件：改变城市下拉列表中的内容


            //导出
            $("#export").click(function () {
                html =  '<div style="display: flex; justify-content: left;flex-wrap: wrap; padding: 10px">' +
                    '<div style="margin-bottom: 20px"><label style="margin-right: 10px; width: 50px"><input name="id"  type="checkbox"  value="id" checked="checked"/>序号</label>' +
                    '<label style="margin-right: 10px; width: 100px"><input name="products"  type="checkbox"  value="level1" checked="checked"/>套餐类型</label>' +
                    '<label style="margin-right: 10px; width: 120px"><input name="platform"  type="checkbox"  value="level2" checked="checked"/>档位（资产数）</label>' +
                    '<label style="margin-right: 10px; width: 120px"><input name="price"  type="checkbox"  value="price" checked="checked"/>价格（$）</label></div>' +
                    '<div><label style="margin-right: 10px; width: 100px"><input name="status"  type="checkbox"  value="status" checked="checked"/>商品状态</label>' +
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

                        location.href = "/admin/goods/saasIndex?export=1&query_type" + query_type + "&info=" + info + "&level1=" + level1 + "&level2=" + level2 +
                            "&start_date=" + startDate + "&end_date=" + endDate + "&status=" + status + "&field=" + field.join(',');
                    }
                });
            });

            //页面预览
            $("#previews").click(function () {
                var url = "{{$pricing_url}}";
                window.open(url, "_blank")
            });
        })
    </script>
@endsection