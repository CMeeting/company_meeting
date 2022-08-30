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
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>修改 Support</h5>
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
                            <input type="text" class="form-control" name="data[version]" value="{{$row->version}}" required disabled="disabled">
                        </div>
                    </div>
                    <textarea id="testt123" style="display: none">{{$parent}}</textarea>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Platform/Products：</label>
                        <div class="col-sm-6 col-xs-12">
                                <select autocomplete="off" class="fenlei form-control ccs" id="category_parent" name="data[platform]" disabled="disabled" onchange="renderCategoryThirdbypcate(this.value)" onclick="renderCategoryThirdbypcate(this.value)">
                                    <option value="0">请选择平台</option>
                                </select>
                                <select autocomplete="off" class="fenlei form-control ccs" id="category_child" name="data[product]"  disabled="disabled" style="margin-left: 5px">
                                    <option value="0">请选择产品</option>
                                </select>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Development Language：</label>
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="data[development_language]" disabled="disabled">
                                    @foreach ($development_language as $k=>$v)
                                        <option value="{{$k}}" @if($k==$row->development_language) selected @endif>{{$v['title']}}</option>
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
                                    <option value="{{$k}}" @if($k==$row->type) selected @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">来源：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[source]" value="{{$row->source}}" required data-msg-required="请输入标题">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">描述：</label>
                        <div class="input-group col-sm-2">
                            <textarea class="form-control" name="data[describe]">{{$row->describe}}</textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">用户邮箱：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" id="e_mail" name="data[e_mail]" value="{{$row->e_mail}}" required data-msg-required="请输入标题">
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

        var c='';
        var $categroys = '';
        var defaultpcate = '';
        var defaultccate = '';
        function chage_specialbysid(){
            $selectChild = $('#category_parent');
            $selectThird = $('#category_child');
            var html = '<option value="0">请选择平台</option>';
            var html1 = '<option value="0">请选择产品</option>';
            for(var i=0; i<$categroys.length; i++){
                if($categroys[i].pid==0){
                    var s = ' ';
                    if($categroys[i].id == defaultpcate) s = 'selected="selected"';
                    html += '<option value="'+$categroys[i].id+'"'+s+'>';
                    // console.log($categroys[i].jid == '0');
                    html +=$categroys[i].name
                    html +='</option>';
                }
            }
            $('#category_parent').html(html);
            $('#category_child').html(html1);
            if(defaultpcate){
                renderCategoryThirdbypcate(defaultpcate)
            }
        }
        function renderCategoryThirdbypcate(pcate){
            console.log($categroys);
            var html1 = '<option value="0">请选择产品</option>';
            for(var i=0; i<$categroys.length; i++){
                if(pcate ==$categroys[i].pid){
                    var s="";
                    if($categroys[i].id == defaultccate) s = 'selected="selected"';
                    html1 += '<option value="'+$categroys[i].id+'"'+s+'>'+$categroys[i].name+'</option>';
                }
            }
            $selectThird.show();
            $selectThird.html(html1);
        }

        $(function (){
            var  selectChilds = $('#category_parent');
            var  selectThirds = $('#category_child');

            c=$("#testt123").text();
            $categroys = JSON.parse(c);
            defaultpcate = parseInt("{{$row->platform}}");
            defaultccate = parseInt("{{$row->product}}");
            $selectChild = $('#category_parent');
            chage_specialbysid();

        })

        $("#add_support").click(function () {
            var e= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!e.test($("#e_mail").val())){
                layer.msg('邮件地址不合法', {time: 1500, anim: 6});
                return false;
            }
            var form_data = new FormData($("#form_data")[0]);
            // form_data.set("data[content]",tinymce.editors[0].getContent());
            $.ajax({
                url: "{{route('support.update',$row->id)}}",
                data: form_data,
                type: 'post',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    console.log(re)
                    if (re.code==200) {
                        layer.msg("修改support成功", {
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