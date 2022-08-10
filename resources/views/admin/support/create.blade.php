@extends('admin.layouts.layout')
@section('content')
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加 Support</h5>
            </div>
            <div class="ibox-content">
{{--                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>--}}
                <a href="{{route('support.list')}}"><button class="btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" style="width: 100%;overflow: auto;">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Version：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[version]" value="" required data-msg-required="请输入标题">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Platform：</label>
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="data[platform]">
                                    @foreach ($platform as $k=>$v)
                                        <option value="{{$k}}">{{$v['title']}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product：</label>
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="data[product]">
                                @foreach ($product as $k=>$v)
                                    <option value="{{$k}}">{{$v['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Development Language：</label>
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="data[development_language]">
                                    @foreach ($development_language as $k=>$v)
                                        <option value="{{$k}}">{{$v['title']}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Type：</label>
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="data[type]">
{{--                                <option value="">暂无type数据</option>--}}
                                @foreach ($type as $k=>$v)--}}
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">来源：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[source]" value="" required data-msg-required="请输入标题">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">描述：</label>
                        <div class="input-group col-sm-2">
                            <textarea class="form-control" name="data[describe]"></textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">用户邮箱：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[e_mail]" value="" required data-msg-required="请输入标题">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">处理人：</label>
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="data[handler]">
                                <option value="">请选择处理人</option>
                                @foreach ($admins as $k=>$v)--}}
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button id="add_support" class="btn btn-primary" type="button"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white reset" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $("#add_support").click(function () {
            var form_data = new FormData($("#form_data")[0]);
            // form_data.set("data[content]",tinymce.editors[0].getContent());
            $.ajax({
                url: "{{route('support.store')}}",
                data: form_data,
                type: 'post',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    console.log(re)
                    if (re.code==200) {
                        layer.msg("添加support成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            $(".reset").click();
                            $(".back").click();
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