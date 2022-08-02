@extends('admin.layouts.layout')
@section('content')
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>编辑Category</h5>
            </div>
            <div class="ibox-content">
{{--                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>--}}
                <a href="{{route('blogs.types')}}"><button class="btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" id="form_data" action="{{ route('blogs.typeUpdate',$row->id) }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title（不允许出现特殊字符）：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[title]" value="{{$row->title}}" required data-msg-required="请输入标题">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Slug(确保唯一性)：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[slug]" value="{{$row->slug}}" required data-msg-required="请输入slug">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Title（不允许出现特殊字符）：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[seo_title]" value="{{$row->seo_title}}" required data-msg-required="请输入Seo Title">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Description：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[description]" value="{{$row->description}}" required data-msg-required="请输入Seo Description">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Keywords：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[keywords]" value="{{$row->keywords}}" required data-msg-required="请输入Seo Keywords">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort id(排序 从小到大)：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="data[sort_id]" value="{{$row->sort_id}}" required data-msg-required="请输入Sort id" min="0" oninput="if(value.length>9)value=value.slice(0,9)">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button id="edit_type" class="btn btn-primary" type="button"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white reset" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $("#edit_type").click(function () {
            var form_data = new FormData($("#form_data")[0]);
            $.ajax({
                url: "{{ route('blogs.typeUpdate',$row->id) }}",
                data: form_data,
                type: 'post',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    console.log(re)
                    if (re.code==200) {
                        layer.msg("修改Category成功", {
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