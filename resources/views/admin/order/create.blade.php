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
    <textarea style="display: none" id="lv3">{{$lv3}}</textarea>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>New Order</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('order.index')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i>
                        返回列表
                    </button>
                </a>
                <form class="form-horizontal" id="forms" name="form" method="post"
                      action="{{route('goods.createrungoods')}}">
                    {{ csrf_field() }}

                    <div class="form-group h1title">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 用户邮箱：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="email" class="form-control" name="data[email]" required placeholder="请输入用户邮箱">
                            <span class="lbl"></span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Full Name：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input name="data[full_name]" id="full_name" class="form-control" placeholder="请输入用户名">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">订单状态：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[status]" id="full_name1" class="form-control">
                                <option value="1">已付款</option>
                                <option value="0">待付款</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Products：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level1][]" id="province1" class="form-control province" onchange="province(1)"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Platform：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level2][]" id="city1" class="form-control city" onchange="city(1)"></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">License Type：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level3][]" id="town1" class="form-control town"></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Period：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[period][]" id="period" class="form-control">
                                <option value="1">1years</option>
                                <option value="2">2years</option>
                                <option value="3">3years</option>
                                <option value="4">4years</option>
                                <option value="5">5years</option>
                            </select>
                        </div>
                    </div>

                    <div id="infosdata1">

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1" ><span id="shebei1">APP ID/Machine ID：</span><span style="color: red;font-size: 14px">*</span></label>
                            <div class="col-sm-6 col-xs-12">
                                <input style="float: left" id="maidian1"  type="text" class="form-control maidian" name="data[appid1][]" required placeholder="APP ID/Machine ID">
                            </div>
                            <span class="lbl" style="float: left;margin-top: 0.2%;display: none;" id="addmaidian1"><a style="display: inline-block;width: 30px;height: 30px;font-size: 18px;color: green;background: #fff9f9;text-align: center;line-height: 30px;border: 1px solid green" onclick="addmaidian(1)">+</a>新增Machine ID</span>
                        </div>

                    </div>

                    <div id="datas" style="margin-top: 10px">
                    </div>

                    <div style="margin-left: 30%;margin-top: 20px;margin-bottom: 30px">
                        <a style="display: inline-block;color: #787575;background: #fff6f6;border: #787575 1px solid;width: 100px;height: 30px;text-align: center;line-height: 30px" onclick="addgoods()">+添加商品</a>
                    </div>
                    <div class="clearfix form-actions">
                        <div class="col-md-offset-3 col-md-9">

                            <a class="btn dropdown-toggle ladda-button" style="background: deepskyblue"
                               data-style="zoom-in" onclick="submits()">
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
        var mdindex = 1;
        var goodsindex=1;
        var proarr;
        var ciarr;
        var toarr;
        function addgoods(){
            goodsindex++;
            var html='<div class="form-group"> <label class="col-sm-2 control-label no-padding-right" for="form-field-1" style="color: #0b94ea"><b>item'+goodsindex+'：</b></label> </div> <div class="form-group"> <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Products：<span style="color: red;font-size: 14px">*</span></label> <div class="col-sm-6 col-xs-12"> <select name="data[level1][]" id="province'+goodsindex+'" class="form-control province" onchange="province('+goodsindex+')"></select> </div> </div> <div class="form-group"> <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Platform：<span style="color: red;font-size: 14px">*</span></label> <div class="col-sm-6 col-xs-12"> <select name="data[level2][]" id="city'+goodsindex+'" class="form-control city" onchange="city('+goodsindex+')"></select> </div> </div> <div class="form-group"> <label class="col-sm-2 control-label no-padding-right" for="form-field-1">License Type：<span style="color: red;font-size: 14px">*</span></label> <div class="col-sm-6 col-xs-12"> <select name="data[level3][]" id="town'+goodsindex+'" class="form-control town"></select></div></div><div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1">Period：<span style="color: red;font-size: 14px">*</span></label> <div class="col-sm-6 col-xs-12"> <select name="data[period][]" id="period" class="form-control"> <option value="1">1years</option> <option value="2">2years</option><option value="3">3years</option> <option value="4">4years</option> <option value="5">5years</option></select> </div> </div> <div id="infosdata'+goodsindex+'"> <div class="form-group"> <label class="col-sm-2 control-label no-padding-right" for="form-field-1"><span id="shebei'+goodsindex+'">APP ID/Machine ID：</span><span style="color: red;font-size: 14px">*</span></label> <div class="col-sm-6 col-xs-12"><input style="float: left" id="maidian'+goodsindex+'"  type="text" class="form-control maidian" name="data[appid'+goodsindex+'][]" required> </div> <span class="lbl" style="float: left;margin-top: 0.2%;display: none;" id="addmaidian'+goodsindex+'"><a style="display: inline-block;width: 30px;height: 30px;font-size: 18px;color: green;background: #fff9f9;text-align: center;line-height: 30px;border: 1px solid green" onclick="addmaidian('+goodsindex+')">+</a>新增Machine ID</span></div></div>';
            $("#datas").append(html);

            $.each(proarr, function () {
                $("#province"+goodsindex).append("<option value='" + this.id + "'>" + this.title + "</option>>")
            })
            var index = $("#province"+goodsindex+" option:checked").index();
            $.each(ciarr[index], function () {

                $("#city"+goodsindex).append("<option value='" + this.id + "'>" + this.title + "</option>>")
            })

            var index1 = $("#province"+goodsindex+" option:checked").index();
            //获取被点击的城市的索引
            var index2 = $("#city"+goodsindex+" option:checked").index();
            $.each(toarr[index1][index2], function () {

                $("#town"+goodsindex).append("<option value='" + this.id + "'>" + this.title + "</option>>");

            })
        }
        function addmaidian(id) {
            mdindex++;
            var str = '<div class="form-group md'+id+'" id="md' + mdindex + '"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"> </label><div class="col-sm-6 col-xs-12"> <input style="float: left" id="maidian"  type="text" class="form-control maidian" name="data[appid'+id+'][]" required> </div> <span class="lbl" style="float: left;margin-top: 0.2%;"><a style="display: inline-block;width: 30px;height: 30px;font-size: 18px;color: red;background: #fff9f9;text-align: center;line-height: 30px;border: 1px solid red" onclick="movemaidian(' + mdindex + ')">-</a></span> </div>';
            if($("#infosdata"+id).children().length==5){
                layer.msg('Machine ID不能超过5个', {time: 1500, anim: 6});
                return false;
            }
            $("#infosdata"+id).append(str);
        }

        function movemaidian(id) {
            $("#md" + id).remove();
        }

        function submits() {
            var dd=2;
            var index = layer.load();
            if(!$("#email").val()){
                layer.close(index);
                layer.msg("请输入用户邮箱", {time: 1500, anim: 6});
                return false;
            }
            var e= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!e.test($("#email").val())){
                layer.close(index);
                layer.msg('邮件地址不合法', {time: 1500, anim: 6});
                return false;
            }
            if(!$("#full_name").val()){
                layer.close(index);
                layer.msg("请输入Full Name", {time: 1500, anim: 6});
                return false;
            }

            $(".province").each(function (){
                if(!$(this).val() || $(this).val()==0){
                    dd=1;
                    layer.close(index);
                    layer.msg("请选择Products", {time: 1500, anim: 6});
                    return false;
                }
            })
            if(dd==1){
                return false;
            }
            $(".city").each(function (){
                if(!$(this).val() || $(this).val()==0){
                    dd=1;
                    layer.close(index);
                    layer.msg("请选择Platform", {time: 1500, anim: 6});
                    return false;
                }
            })
            if(dd==1){
                return false;
            }
            $(".town").each(function (){
                if(!$(this).val() || $(this).val()==0){
                    dd=1;
                    layer.close(index);
                    layer.msg("请选择License Type", {time: 1500, anim: 6});
                    return false;
                }
            })
            if(dd==1){
                return false;
            }
            $(".maidian").each(function (){
                if(!$(this).val() || $(this).val()==""){
                    dd=1;
                    layer.close(index);
                    layer.msg("有APPID/Machine ID为空", {time: 1500, anim: 6});
                    return false;
                }
            })

            if(dd==1){
                return false;
            }
            var form_data = new FormData($("#forms")[0]);
            layer.close(index);

            $.ajax({
                url: "{{route('order.createrun')}}",
                processData: false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType: false,
                type: "post",
                data: form_data,
                success: function (data) {
                    if (data.code == 200) {
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


        function province(id){
            //获取被点击的省份的索引
            var index = $("#province"+id+" option:checked").index();

            //先清空城市下拉列表中的内容
            $("#city"+id).empty();

            //根据获得的省份索引，遍历城市数组中对应的索引中的内容，将内容添加到城市下拉列表中

            $.each(ciarr[index], function () {

                $("#city"+id).append("<option value='" + this.id + "'>" + this.title + "</option>>")

            })

            //获得被点击的省份的索引
            var index1 = $("#province"+id+" option:checked").index();
            //获取被点击的城市的索引
            var index2 = $("#city"+id+" option:checked").index();

            //清空县区下拉列表中的内容
            $("#town"+id).empty();

            //根据被点击的省份和城市索引，遍历县区数组中对应的索引中的内容，将内容添加到县区下拉列表中去
            $.each(toarr[index1][index2], function () {

                $("#town"+id).append("<option value='" + this.id + "'>" + this.title + "</option>>");

            })
        }


        function city(id){
            //获得被点击的省份的索引
            var index1 = $("#province"+id+" option:checked").index();
            //获取被点击的城市的索引
            var index2 = $("#city"+id+" option:checked").index();

            if($("#city"+id+" option:checked").text()=="Windows"){
                $("#addmaidian"+id).show();
                $("#shebei"+id).html("Machine ID：");
                $("#maidian"+id).attr("placeholder","Machine ID");
            }else{
                $("#addmaidian"+id).hide();
                $(".md"+id).remove();
                $("#shebei"+id).html("APPID：");
                $("#maidian"+id).attr("placeholder","APPID");
            }
            //清空县区下拉列表中的内容
            $("#town"+id).empty();

            //根据被点击的省份和城市索引，遍历县区数组中对应的索引中的内容，将内容添加到县区下拉列表中去
            $.each(toarr[index1][index2], function () {
                $("#town"+id).append("<option value='" + this.id + "'>" + this.title + "</option>>");
            })
        }

        $(function () {
             proarr = JSON.parse($("#lv1").text());
             ciarr = JSON.parse($("#lv2").text());
             toarr = JSON.parse($("#lv3").text());
            //遍历省份数组，将省份添加到省份下拉列表中
            $.each(proarr, function () {
                $("#province1").append("<option value='" + this.id + "'>" + this.title + "</option>>")
            })
            var index = $("#province1 option:checked").index();
            $.each(ciarr[index], function () {

                $("#city1").append("<option value='" + this.id + "'>" + this.title + "</option>>")
            })

            var index1 = $("#province1 option:checked").index();
            //获取被点击的城市的索引
            var index2 = $("#city1 option:checked").index();
            $.each(toarr[index1][index2], function () {
                $("#town1").append("<option value='" + this.id + "'>" + this.title + "</option>>");
            })

        })


    </script>
@endsection
