@extends('admin.layouts.layout')
@section('content')
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>修改密码</h5>
        </div>
        <div class="ibox-content">
{{--            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>--}}
{{--            <a href="{{route('admins.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>--}}
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">原始密码：</label>
                        <div class="input-group col-sm-2">
                            <input type="password" class="form-control" name="old_password">
                        </div>
                    </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">新密码：</label>
                    <div class="input-group col-sm-2">
                        <input type="password" class="form-control" name="new_password">
                    </div>
                </div>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">确认新密码：</label>
                    <div class="input-group col-sm-2">
                        <input type="password" class="form-control" name="check_password">
                    </div>
                </div>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <div class="col-sm-12 col-sm-offset-2">
                        <button id="edit_password" class="btn btn-primary" type="button"><i class="fa fa-check"></i>&nbsp;保 存</button>
                        <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
</div>
<script>
    $("#edit_password").click(function () {
        var form_data = new FormData($("#form_data")[0]);
        $.ajax({
            url: "{{ route('admin.updatePassword',$admin['id']) }}",
            data: form_data,
            type: 'post',
            processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
            contentType:false,
            // dataType: "json",
            success: function (re) {
                //成功提示
                console.log(re)
                if (re.code==200) {
                    layer.msg(re.msg, {
                        icon: 1,
                        time: 2000
                    }, function () {
                        window.location.href='{{route('admin.logout')}}'
                    });
                } else {
                    //失败提示
                    if(re.msg){
                        layer.msg(re.msg, {
                            icon: 2,
                            time: 2000
                        });
                    }else {
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