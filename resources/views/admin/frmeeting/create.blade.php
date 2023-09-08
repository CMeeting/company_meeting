@extends('admin.layouts.layout')
@section('content')

    <style>
        dl.layui-anim.layui-anim-upbit {
            z-index: 1000;
        }
        .ccs{
            width: calc(49.5%);
            float: left;
        }
    </style>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script src="/tinymce/js/tinymce/tinymce.min.js"></script>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <script src="/js/bootstrap/bootstrap.min.js"></script>
    <script src="/js/bootstrap/countrypicker.min.js"></script>
    <script src="/js/bootstrap/bootstrap-select.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/bootstrap/bootstrap-select.css"/>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Create Meeting</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('frmeeting.list')}}"><button class="btn btn-primary btn-sm back" type="button" style="margin-bottom: 40px"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" style="width: 600px;margin: 0 auto;">

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Topic(fr)：<font style="color: red;font-size: 14px">*</font></span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input id="topic_fr" type="text" placeholder="*Topic(fr)" class="form-control" name="topic_fr" value="" required data-msg-required="topic required" style="width: 500px"/>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Topic(eng)：<font style="color: red;font-size: 14px">*</font></span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input id="topic_eng" type="text" placeholder="*Topic(eng)" class="form-control" name="topic_eng" value="" required data-msg-required="topic required" style="width: 500px"/>
                        </div>
                    </div>


                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Speaker：<font style="color: red;font-size: 14px">*</font></span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        @foreach($speaker_arr as $key=>$type)
                            <div class="layui-col-xs5">
                                <input type="checkbox" name="user_id[{{$type['id']}}]" lay-skin="primary" title="{{$type['name']}}">{{$type['name']}}
                            </div>
                        @endforeach
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Date：<font style="color: red;font-size: 14px">*</font></span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group-btn" style="display: inline-block;width: auto;">
                            <input type="text" name="date" class="form-control" style="display: inline-block;width: 160px;" id="date" placeholder="Meeting Date" value=""/>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Start&End Time：<font style="color: red;font-size: 14px">*</font></span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group-btn" style="display: inline-block;width: auto;">
                            <input type="text" name="start_time" class="form-control" style="display: inline-block;width: 160px;" id="start_time" placeholder="Start Time" value=""/>
                            <input type="text" name="end_time" class="form-control" style="display: inline-block;width: 160px;" id="end_time" placeholder="End time" value=""/>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px; margin-left: 33%">
                        <button id="add_meeting" class="btn btn-primary" type="button"><i class="fa fa-check"></i>&nbsp;提交</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $("#add_meeting").click(function () {
            let form_data = new FormData($("#form_data")[0]);
            let index = layer.load();

            let email = form_data.get('email').trim()
            if(email == '' || email == null){
                layer.close(index);
                layer.msg('邮箱必填', {icon: 2, time: 1000});
                return false;
            }

            let e = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!e.test(email)){
                layer.close(index);
                layer.msg('请输入有效邮箱', {icon: 2, time: 1000});
                $("#email_input").css('border', '1px solid red')
                return false;
            }

            let full_name = form_data.get('full_name').trim()
            if(full_name == '' || full_name == null){
                layer.close(index);
                layer.msg('Full Name必填', {icon: 2, time: 1000});
                return false;
            }

            let company = form_data.get('company').trim()
            if(company == '' || company == null){
                layer.close(index);
                layer.msg('Company必填', {icon: 2, time: 1000});
                return false;
            }

            let country = form_data.get('country').trim()
            if(country == '' || country == null || country == 'All'){
                layer.close(index);
                layer.msg('country必选', {icon: 2, time: 1000});
                return false;
            }

            $.ajax({
                url: "{{route('user.store')}}",
                data: form_data,
                type: 'post',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    console.log(re)
                    if (re.code==200) {
                        layer.close(index);
                        layer.open({
                            content:'用户添加成功',
                            btn: ['确认'],
                            title:'提交成功',
                            yes: function (index, layero) {
                                location.href = "{{route('user.list')}}"
                            }
                        })
                    } else {
                        layer.close(index);
                        //失败提示
                        if(re.msg){
                            layer.open({
                                content:re.msg,
                                btn: ['确认'],
                                title:'提交失败',
                            })
                        }else {
                            layer.close(index);
                            layer.msg("请检查网络或权限设置！！！", {
                                icon: 2,
                                time: 2000
                            });
                        }
                    }
                }
            });
        })

        layui.use('laydate', function () {
            let laydate = layui.laydate;

            //执行一个laydate实例
            let start = laydate.render({
                elem: '#start_time', //指定元素
                max: 1,//最大值为当前日期
                trigger: 'click',
                type: 'date',//日期时间选择器
                done: function (value, date) {
                    if (value && (value > $("#end_time").val())) {
                        /*开始时间大于结束时间时，清空结束时间*/
                        $("#end_time").val("");
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
            let end = laydate.render({
                elem: '#end_time', //指定元素
                max: 30,//最大值为当前日期
                type: 'day',//日期时间选择器
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });

        layui.use('laydate', function () {
            let laydate = layui.laydate;
            let end = laydate.render({
                elem: '#date', //指定元素
                max: 30,//最大值为当前日期
                type: 'day',//日期时间选择器
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });

    </script>
@endsection