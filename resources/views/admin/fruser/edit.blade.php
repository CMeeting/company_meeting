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
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
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
                <h5>编辑资料</h5>
            </div>
            <div class="ibox-content">
                <a href="javascript:history.back(-1)"><button class="btn btn-primary btn-sm back" type="button" style="margin-bottom: 40px"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" style="width: 600px;margin: 0 auto;">
                    {!! csrf_field() !!}
                    <span style="color: red;font-size: 14px">*</span>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Email：</span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input id="email_input" type="text" placeholder="*Email" class="form-control" name="email" value="{{$row->email}}" required data-msg-required="邮箱必填" style="width: 500px"/>
                        </div>
                    </div>
                    <span style="color: red;font-size: 14px">*</span>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Full Name：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" placeholder="*Full Name" class="form-control" name="full_name" value="{{$row->full_name ?? ''}}" required data-msg-required="Full Name必填" style="width: 500px"/>
                        </div>
                    </div>

                    <span style="color: red;font-size: 14px">*</span>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Company：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" placeholder="*Company" class="form-control" name="company" value="{{$company}}" required data-msg-required="Company必填" style="width: 500px"/>
                        </div>
                    </div>

                    <span style="color: red;font-size: 14px">*</span>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Country：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="form-group" style="padding-left: 18px;width: 500px">
                            <select id="country" name="country" class="selectpicker countrypicker" data-live-search="true" data-default="{{$country}}" data-flag="true"></select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button id="add_user" class="btn btn-primary" type="button" style="margin-right: 30px"><i class="fa fa-check"></i>&nbsp;提交</button>
                            <button id="reset_password" class="btn btn-danger" type="button" style="margin-left: 30px"><i class="layui-icon layui-icon-password"></i>重置密码</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $("#add_user").click(function () {
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
                url: "{{route('user.update', $row->id)}}",
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
                            content:'用户资料编辑成功',
                            btn: ['确认'],
                            title:'提交成功',
                            yes: function (index, layero) {
                                location.href = "{{route('user.list')}}";
                            }
                        })
                    } else {
                        layer.close(index);
                        //失败提示
                        if(re.msg){
                            layer.open({
                                content:re.msg,
                                btn: ['确认'],
                                title:'提交失败'
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

        $("#reset_password").click(function () {
            let index = layer.load();
            $.ajax({
                url: "{{route('user.resetPassword', $row->id)}}",
                data: '',
                type: 'get',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    if (re.code==200) {
                        layer.close(index);
                        layer.open({
                            content:'重置密码成功',
                            btn: ['确认'],
                            title:'提交成功',
                            yes: function (index, layero) {
                                window.history.go(-1);
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
    </script>
@endsection