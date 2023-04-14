@extends('admin.layouts.layout')
@section('content')
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
<script>
    import InlineView from "../../../../public/tinymce/modules/alloy/src/main/ts/ephox/alloy/api/ui/InlineView";
    export default {
        components: {InlineView}
    }
</script>
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
                <h5>New license</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('license.index')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i>
                        返回列表
                    </button>
                </a>
                <form class="form-horizontal" id="forms" name="form"  method="post" action="{{route('goods.createrungoods')}}" >
                    {{ csrf_field() }}

                    <div class="form-group h1title">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 用户邮箱：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="email" class="form-control" name="data[email]" required placeholder="请输入用户邮箱">
                            <span class="lbl"></span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">客户公司名称：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input name="data[company_name]" id="company_name" class="form-control" placeholder="Company Name"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">序列码类型：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <select id="type" class="form-control" name="data[type]" tabindex="1">
                                <option value="0">请选择序列码类型</option>
                                @foreach($license_type as $key => $value)
                                    <option value="{{$key}}" @if(isset($query)&&$query['type']==$key) selected @endif>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">产品：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                        <select name="data[level1]" id="province" class="form-control"></select>
                    </div>
                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">平台：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                        <select name="data[level2]" id="city" class="form-control"></select>
                    </div>
                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">功能套餐类型：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                        <select name="data[level3]" id="town" class="form-control"></select>
                    </div>
                </div>
                    <div id="infosdata1">

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1" ><span id="shebei">APP ID/Machine ID：</span><span style="color: red;font-size: 14px">*</span></label>
                            <div class="col-sm-6 col-xs-12">
                                <input style="float: left" id="maidian"  type="text" class="form-control maidian" name="data[appid][]" required placeholder="APP ID/Machine ID">
                            </div>
                            <span class="lbl" style="float: left;margin-top: 0.2%;display: none" id="addmaidian"><a style="display: inline-block;width: 30px;height: 30px;font-size: 18px;color: green;background: #fff9f9;text-align: center;line-height: 30px;border: 1px solid green" onclick="addmaidian(1)">+</a>新增Machine ID</span>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">序列码有效期：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input name="data[period]" id="period" class="form-control" placeholder="单位为月(一年则填写12)"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">上传密钥文件：<span style="color: red;font-size: 14px">*</span></label>
                        <div class="col-sm-6 col-xs-12">
                            <input type="file" name="file" id="file"/>
{{--                            <button type="button" class="layui-btn" id="upload">--}}
{{--                                <i class="layui-icon">&#xe67c;</i>--}}
{{--                            </button>--}}
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
        var mdindex=0;
        function addmaidian(id) {
            mdindex++;
            var str = '<div class="form-group md" id="md' + mdindex + '"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"> </label><div class="col-sm-6 col-xs-12"> <input style="float: left" id="maidian"  type="text" class="form-control maidian" name="data[appid][]" required> </div> <span class="lbl" style="float: left;margin-top: 0.2%;"><a style="display: inline-block;width: 30px;height: 30px;font-size: 18px;color: red;background: #fff9f9;text-align: center;line-height: 30px;border: 1px solid red" onclick="movemaidian(' + mdindex + ')">-</a></span> </div>';
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
            if(!$("#company_name").val()){
                layer.msg("请输入Company Name", {time: 1500, anim: 6});
                return false;
            }
            if(!$("#type").val()||$("#province").val()==0){
                layer.msg("请选择序列码类型", {time: 1500, anim: 6});
                return false;
            }
            if(!$("#province").val()||$("#province").val()==0){
                layer.msg("请选择Products", {time: 1500, anim: 6});
                return false;
            }
            if(!$("#city").val()||$("#city").val()==0){
                layer.msg("请选择Platform", {time: 1500, anim: 6});
                return false;
            }
            if(!$("#town").val()||$("#town").val()==0){
                layer.msg("请选择License Type", {time: 1500, anim: 6});
                return false;
            }
            $(".maidian").each(function (){
                if(!$(this).val()){
                    dd=1;
                    layer.msg("有APPID/Machine ID为空", {time: 1500, anim: 6});
                    return false;
                }
            })
            var x = document.getElementById("file").value;
            if(x == ''){
                layer.msg("没有上传密钥文件", {time: 1500, anim: 6});
                return false;
            }

            //限制文件上传大小不能超过1M
            var keyFile = document.getElementById("file").files[0]
            var maxsize = 1024*1024;
            if(keyFile.size > maxsize){
                layer.msg("上传文件不能大于1M", {time: 1500, anim: 6});
                return false;
            }

            //限制文件上传类型
            var suffix_arr = keyFile.name.split('.')
            var suffix = suffix_arr[suffix_arr.length-1]
            if(suffix != 'pem'){
                layer.msg("请上传.pem后缀文件", {time: 1500, anim: 6});
                return false;
            }

            if(dd==1){
                return false;
            }
            var form_data = new FormData($("#forms")[0]);

            var index = layer.load();

            $.ajax({
                url: "{{route('license.createrunLicense')}}",
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
                    layer.close(index);
                }
            })
        }

        $(function () {
            var proarr = JSON.parse($("#lv1").text());
            var ciarr = JSON.parse($("#lv2").text());
            var toarr = JSON.parse($("#lv3").text());

            console.log(toarr);
            //遍历省份数组，将省份添加到省份下拉列表中
            $.each(proarr, function () {

                    $("#province").append("<option value='" + this.id + "'>" + this.title + "</option>>")

            })
            var index = $("#province option:checked").index();
            $.each(ciarr[index], function () {

                    $("#city").append("<option value='" + this.id + "'>" + this.title + "</option>>")
                })

            var index1 = $("#province option:checked").index();
            //获取被点击的城市的索引
            var index2 = $("#city option:checked").index();
            $.each(toarr[index1][index2], function () {

                    $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");

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

                //获得被点击的省份的索引
                var index1 = $("#province option:checked").index();
                //获取被点击的城市的索引
                var index2 = $("#city option:checked").index();

                //清空县区下拉列表中的内容
                $("#town").empty();

                //根据被点击的省份和城市索引，遍历县区数组中对应的索引中的内容，将内容添加到县区下拉列表中去
                $.each(toarr[index1][index2], function () {

                        $("#town").append("<option value='" + this.id + "'>" + this.title + "</option>>");

                })
            })

            //创建一个用户改变域的内容的事件：改变城市下拉列表中的内容
            $("#city").change(function () {

                //获得被点击的省份的索引
                var index1 = $("#province option:checked").index();
                //获取被点击的城市的索引
                var index2 = $("#city option:checked").index();

                if($("#city option:checked").text()=="Windows"){
                    $("#addmaidian").show();
                    $("#shebei").html("Machine ID：");
                    $("#maidian").attr("placeholder","Machine ID");
                }else{
                    $("#addmaidian").hide();
                    $(".md").remove();
                    $("#shebei").html("APPID：");
                    $("#maidian").attr("placeholder","APPID");
                }
                //清空县区下拉列表中的内容
                $("#town").empty();

                //根据被点击的省份和城市索引，遍历县区数组中对应的索引中的内容，将内容添加到县区下拉列表中去
                $.each(toarr[index1][index2], function () {

                        $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");

                })
            })


        })


    </script>
@endsection
