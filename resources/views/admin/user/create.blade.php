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
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加用户</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('user.list')}}"><button class="btn btn-primary btn-sm back" type="button" style="margin-bottom: 40px"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" style="width: 600px;overflow: auto;margin: 0 auto;">
                    {!! csrf_field() !!}
                        <span style="color: red;font-size: 14px">*</span>
                        <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Email：</span></div>
                        <div class="form-group" style="padding-left: 18px;">
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control" name="email" value="" required data-msg-required="邮箱必填" style="width: 500px"/>
                            </div>
                        </div>
                        <span style="color: red;font-size: 14px">*</span>
                        <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Full Name：</span>></div>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="full_name" value="" required data-msg-required="Full Name必填" style="width: 500px"/>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <div class="col-sm-12 col-sm-offset-2">
                                <button id="add_user" class="btn btn-primary" type="button" style="margin-right: 30px"><i class="fa fa-check"></i>&nbsp;提交</button>
                                <a href="{{route('user.list')}}"><button class="btn btn-danger" type="button" style="margin-left: 30px"><i class="layui-icon layui-icon-return"></i>取消</button></a>
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
                layer.msg('邮箱地址不合法', {icon: 2, time: 1000});
                return false;
            }

            let full_name = form_data.get('full_name').trim()
            if(full_name == '' || full_name == null){
                layer.close(index);
                layer.msg('Full Name必填', {icon: 2, time: 1000});
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
                        layer.msg("添加用户成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            $(".reset").click();
                            $(".back").click();
                        });
                    } else {
                        layer.close(index);
                        //失败提示
                        if(re.msg){
                            layer.msg(re.msg, {
                                icon: 2,
                                time: 2000
                            });
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