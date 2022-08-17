@extends('admin.layouts.layout')
@section('content')
    <style>
        .ccs{
            width: calc(100%);
        }

    </style>
    <script src="{{loadEdition('/tinymce/js/tinymce/tinymce.min.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>New Subscription</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('newsletter.subscription_list')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i> 返回列表
                    </button>
                </a>
                <form class="form-horizontal" id="forms" name="form"  method="post" action="{{route('newsletter.createrunsubscription')}}" >
                    {{ csrf_field() }}


                    <div class="form-group" style="margin-top: 20px">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> emali(订阅人邮件)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="name"  class="form-control" name="data[email]" required maxlength="255">
                            <span class="lbl"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> status(是否订阅)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input type="radio" name="data[status]" value="1" checked >订阅状态
                            <input type="radio" name="data[status]" value="0">取消订阅
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

        function submits(){
            var form_data = new FormData($("#forms")[0]);

            layer.close(index);
            var index = layer.load();
            var e= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!e.test($("#name").val())){
                layer.close(index);
                layer.msg('邮件地址不合法', {time: 1500, anim: 6});
                return false;
            }
            $.ajax({
                url:"{{route('newsletter.createrunsubscription')}}",
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                type:"post",
                data: form_data,
                success:function(data){
                    if(data.code==1){
                        layer.close(index);
                        layer.msg("添加成功", {time: 1500, anim: 1});
                        $(".reset").click();
                        $(".back").click();
                    }else{
                        layer.close(index);
                        layer.msg(data.msg, {time: 1500, anim: 6});
                        return false;
                    }
                }, error:function(ret){

                }
            })

        }
    </script>
@endsection
