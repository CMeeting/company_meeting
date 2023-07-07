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
                <h5>新增订单</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('order.saasindex')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i>
                        返回列表
                    </button>
                </a>
                <form class="form-horizontal" id="forms" name="form" method="post">
                    {{ csrf_field() }}

                    <div class="form-group h1title">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 用户邮箱：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="email" class="form-control" name="data[email]" required placeholder="请输入用户邮箱">
                            <span class="lbl"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">订单状态：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[status]" id="full_name1" class="form-control" disabled>
                                <option value="1">已付款</option>
{{--                                <option value="0">待付款</option>--}}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">套餐：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level1]" id="province1" class="form-control province" onchange="province(1)"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">档位（资产数）：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level2]" id="city1" class="form-control city" onchange="city(1)"></select>
                        </div>
                    </div>

                    <div id="peizhi" style="display: none">
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1">配置资产数量：<span style="color: red;font-size: 14px">*</span></label>
                            <div class="col-sm-6 col-xs-12">
                                <input name="data[special_assets]" id="zican" class="form-control" type="number" min="0" max="99999999" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1">价格（USD）：<span style="color: red;font-size: 14px">*</span></label>
                            <div class="col-sm-6 col-xs-12">
                                <input name="data[price]" id="price" class="form-control" type="number" min="0.01" max="99999999" step="0.01" oninput="if(value.length>8)value=value.slice(0,8)" value="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group h1title" id="pay_yearsdiv">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 有效期：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="pay_years" type="number" min="1" class="form-control" name="data[pay_years]" required placeholder="请输入有效期">
                        </div>
                        <span class="lbl">/月</span>
                    </div>



                    <div class="clearfix form-actions">
                        <div class="col-md-offset-3 col-md-9">

                            <a class="btn dropdown-toggle ladda-button" style="background: deepskyblue"
                               data-style="zoom-in" id="baochun" onclick="submits()">
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

        var proarr;
        var ciarr;




        function submits() {
            layer.load();
            var dd=2;
            var index = layer.load();
            if(!$("#email").val()){
                layer.closeAll('loading');
                layer.close(index);
                layer.msg("请输入用户邮箱", {time: 1500, anim: 6});
                return false;
            }
            var e= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!e.test($("#email").val())){
                layer.closeAll('loading');
                layer.close(index);
                layer.msg('邮件地址不合法', {time: 1500, anim: 6});
                return false;
            }

            $(".province").each(function (){
                if(!$(this).val() || $(this).val()==0){
                    dd=1;
                    layer.closeAll('loading');
                    layer.close(index);
                    layer.msg("请选择套餐", {time: 1500, anim: 6});
                    return false;
                }
            })
            if(dd==1){
                layer.closeAll('loading');
                return false;
            }

            if($("#province1 option:checked").text()!="Package" && !$("#pay_years").val()){
                dd=1;
                layer.msg("请输入有效期", {time: 1500, anim: 6});
                layer.closeAll('loading');
                return false;
            }

            $(".city").each(function (){
                if(!$(this).val() || $(this).val()==0){
                    dd=1;
                    layer.close(index);
                    layer.msg("请选择档位", {time: 1500, anim: 6});
                    return false;
                }
            })
            if(dd==1){
                layer.closeAll('loading');
                return false;
            }

            var form_data = new FormData($("#forms")[0]);

            $.ajax({
                url: "{{route('order.saascreaterun')}}",
                processData: false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType: false,
                type: "post",
                data: form_data,
                success: function (data) {
                    if (data.code == 200) {
                        layer.closeAll('loading');
                        layer.close(index);
                        layer.msg("添加成功", {time: 1500, anim: 6}, function(){
                            $(".reset").click();
                            $(".back").click();
                        });
                    } else {
                        layer.closeAll('loading');
                        layer.close(index);
                        layer.msg(data.message, {time: 1500, anim: 6});
                        return false;
                    }
                }, error: function (ret) {

                }
            })
        }


        function province(id){
            //获取被点击的省份的索引
            var index = $("#province"+id+" option:checked").index();

            if($("#province"+id+" option:checked").text()=="Package"){
                $("#pay_yearsdiv").hide();
            }else{
                $("#pay_yearsdiv").show();
            }
            //先清空城市下拉列表中的内容
            $("#city"+id).empty();

            //根据获得的省份索引，遍历城市数组中对应的索引中的内容，将内容添加到城市下拉列表中

            $.each(ciarr[index], function () {

                $("#city"+id).append("<option value='" + this.id + "'>" + this.title + "</option>>")

            })

        }


        function city(id){
            if($("#city"+id+" option:checked").text()=="手动配置"){
                $("#peizhi").show();
            }else{
                $("#peizhi").hide();
            }
        }

        $(function () {
             proarr = JSON.parse($("#lv1").text());
             ciarr = JSON.parse($("#lv2").text());

            //遍历省份数组，将省份添加到省份下拉列表中
            $.each(proarr, function () {
                $("#province1").append("<option value='" + this.id + "'>" + this.title + "</option>>")
            })
            var index = $("#province1 option:checked").index();
            $.each(ciarr[index], function () {

                $("#city1").append("<option value='" + this.id + "'>" + this.title + "</option>>")
            })
        })


    </script>
@endsection
