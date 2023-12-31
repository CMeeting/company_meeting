@extends('admin.layouts.layout')
@section('content')
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <style>
        .cc {
            display: block;
            width: 100%;
            padding-left: 10px;
            height: 38px;
            line-height: 1.3;
            line-height: 38px \9;
            border-width: 1px;
            border-style: solid;
            background-color: #fff;
            color: rgba(0, 0, 0, .85);
            border-radius: 2px;
        }
    </style>

    <textarea style="display: none" id="lv1">{{$lv1}}</textarea>
    <textarea style="display: none" id="lv2">{{$lv2}}</textarea>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>新增商品</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('goods.saasIndex')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i>
                        返回列表
                    </button>
                </a>
                <form class="form-horizontal" id="forms" name="form"  method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">套餐：</label>
                        <div class="col-sm-6 col-xs-12">
                        <select name="data[level1]" id="province" class="form-control"></select>
                    </div>
                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">档位（资产数）：</label>
                        <div class="col-sm-6 col-xs-12">
                        <select name="data[level2]" id="city" class="form-control"></select>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 价格（$）：</label>
                    <div class="col-sm-6 col-xs-12">
                        <input style="float: left" id="price" type="number" class="form-control" name="data[price]" min="0" max="99999999" step="0.01" oninput="if(value.length>8)value=value.slice(0,8)" value="0.00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商品状态：</label>
                    <div class="col-sm-6 col-xs-12">
                        <input type="radio" name="data[status]" value="0" checked>待上架
                        <input type="radio" name="data[status]" value="1">架上商品
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 排序：</label>
                    <div class="col-sm-6 col-xs-12">
                        <input id="displayorder"  type="number" class="form-control" name="data[sort_num]" min="1" max="99999999" oninput="if(value.length>8)value=value.slice(0,8)" value="{{$sort}}" required>
                        <span class="lbl">商品的排序对应API官网Pricing页面展示顺序，月订阅 > 年订阅 > 打包购买（官网tab顺序），同一套餐类型下的商品在官网会根据序号升序排列</span>
                    </div>
                </div>


                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">

                        <a class="btn dropdown-toggle ladda-button"    style="background: deepskyblue" data-style="zoom-in" onclick="submits()">
                            保&nbsp;&nbsp;存
                        </a>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-white reset" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>

    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script>

        function submits() {
            var index = layer.load();

            if(!$("#province").val()||$("#province").val()==0){
                layer.close(index);
                layer.msg("请选择套餐", {time: 1500, anim: 6});
                return false;
            }
            if(!$("#city").val()||$("#city").val()==0){
                layer.close(index);
                layer.msg("请选择档位", {time: 1500, anim: 6});
                return false;
            }

            if(!$("#price").val()){
                layer.close(index);
                layer.msg("价格不能为空", {time: 1500, anim: 6});
                return false;
            }

            if($("#price").val() < 0.00){
                layer.close(index);
                layer.msg("价格不能低于0.00", {time: 1500, anim: 6});
                return false;
            }

            if(!$("#displayorder").val()){
                layer.close(index);
                layer.msg("排序不能为空", {time: 1500, anim: 6});
                return false;
            }

            var form_data = new FormData($("#forms")[0]);
            layer.close(index);

            $.ajax({
                url: "{{route('goods.createrunsaasgoods')}}",
                processData: false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType: false,
                type: "post",
                data: form_data,
                success: function (data) {
                    console.log(data)
                    if (data.code == 1) {
                        layer.close(index);
                        layer.msg("添加成功", {time: 1500, anim: 1});
                        $(".reset").click();
                        $(".back").click();
                    } else {
                        layer.close(index);
                        layer.msg(data.msg, {time: 1500, anim: 6});
                        return false;
                    }
                }
            })
        }

        $(function () {
            var proarr = JSON.parse($("#lv1").text());
            var ciarr = JSON.parse($("#lv2").text());

            //遍历省份数组，将省份添加到省份下拉列表中
            $.each(proarr, function () {

                    $("#province").append("<option value='" + this.id + "'>" + this.title + "</option>>")

            })
            var index = $("#province option:checked").index();
            $.each(ciarr[index], function () {

                    $("#city").append("<option value='" + this.id + "'>" + this.title + "</option>>")
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



        })


    </script>
@endsection
