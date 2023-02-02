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
                <h5>New SaaSGoods</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('goods.saasindex')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i>
                        返回列表
                    </button>
                </a>
                <form class="form-horizontal" id="forms" name="form"  method="post" action="{{route('goods.createrunsaasgoods')}}" >
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">套餐：</label>
                        <div class="col-sm-6 col-xs-12">
                        <select name="data[level1]" id="province" class="form-control"></select>
                    </div>
                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">档位：</label>
                        <div class="col-sm-6 col-xs-12">
                        <select name="data[level2]" id="city" class="form-control"></select>
                    </div>
                </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Pricing(USD)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input style="float: left" id="price"  type="number" class="form-control" name="data[price]" min="0.01" max="99999999" step="0.01" oninput="if(value.length>8)value=value.slice(0,8)" value="0.00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> status(是否上架)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input type="radio" name="data[status]" value="1" checked >上架
                            <input type="radio" name="data[status]" value="0">下架
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

            if($("#price").val() < 0.01){
                layer.close(index);
                layer.msg("价格不能低于0.01", {time: 1500, anim: 6});
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
                }, error: function (ret) {

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
