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
                <h5>Edit Goods</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('goods.index')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i>
                        返回列表
                    </button>
                </a>
                <form class="form-horizontal" id="forms" name="form"  method="post" action="{{route('goods.updaterungoods')}}" >
                    {{ csrf_field() }}
                    <input type="hidden" name="data[id]" value="{{$data['id']}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Products：</label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level1]" id="province" class="form-control" style="pointer-events: none;color: #9f9f9f"></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Platform：</label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level2]" id="city" class="form-control" style="pointer-events: none;color: #9f9f9f"></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">License Type：</label>
                        <div class="col-sm-6 col-xs-12">
                            <select name="data[level3]" id="town" class="form-control" style="pointer-events: none;color: #9f9f9f"></select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Pricing(USD)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input style="float: left" id="price"  type="number" class="form-control" name="data[price]" min="0.01" max="99999999" step="0.01" oninput="if(value.length>8)value=value.slice(0,8)" value="{{$data['price']}}" required>
                        </div>
                        <span class="lbl" style="float: left;margin-top: 0.5%">/year</span>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> status(是否上架)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input type="radio" name="data[status]" value="1" @if($data['status']==1) checked @endif >上架
                            <input type="radio" name="data[status]" value="0" @if($data['status']==0) checked @endif>下架
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

            if(!$("#province").val()){
                layer.close(index);
                layer.msg("请选择Products", {time: 1500, anim: 6});
                return false;
            }
            if(!$("#city").val()){
                layer.close(index);
                layer.msg("请选择Platform", {time: 1500, anim: 6});
                return false;
            }
            if(!$("#town").val()){
                layer.close(index);
                layer.msg("请选择License Type", {time: 1500, anim: 6});
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
                url: "{{route('goods.updaterungoods')}}",
                processData: false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType: false,
                type: "post",
                data: form_data,
                success: function (data) {
                    if (data.code == 1) {
                        layer.close(index);
                        layer.msg("编辑成功", {time: 1500, anim: 1});
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
            var toarr = JSON.parse($("#lv3").text());
            var level1 = {{$data['level1']}};
            var level2 = {{$data['level2']}};
            var level3 = {{$data['level3']}};
            console.log(toarr);
            //遍历省份数组，将省份添加到省份下拉列表中
            $.each(proarr, function () {
                if(this.id==level1){
                    $("#province").append("<option value='"+this.id+"' selected>" + this.title + "</option>>")
                }else{
                    if(this.id>0){
                        $("#province").append("<option value='"+this.id+"'>" + this.title + "</option>>");
                    }
                }

            })
            var index = $("#province option:checked").index();

            $.each(ciarr[index], function () {
                if(this.id==level2){
                    $("#city").append("<option value='"+this.id+"' selected>" + this.title + "</option>>")
                }else {
                    if(this.id>0){
                        $("#city").append("<option value='"+this.id+"'>" + this.title + "</option>>");
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
                    if(this.id>0){
                        $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");
                    }
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
                    if(this.id>0) {
                        $("#city").append("<option value='" + this.id + "'>" + this.title + "</option>>")
                    }
                })
                var index1 = $("#province option:checked").index();
                //获取被点击的城市的索引
                var index2 = $("#city option:checked").index();

                //清空县区下拉列表中的内容
                $("#town").empty();

                //根据被点击的省份和城市索引，遍历县区数组中对应的索引中的内容，将内容添加到县区下拉列表中去
                $.each(toarr[index1][index2], function () {
                    if(this.id>0){
                        $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");
                    }
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
                    if(this.id>0){
                        $("#town").append("<option value='"+this.id+"'>" + this.title + "</option>>");
                    }
                })
            })


        })


    </script>
@endsection
