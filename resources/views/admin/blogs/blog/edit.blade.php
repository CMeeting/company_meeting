@extends('admin.layouts.layout')
@section('content')
    <script src="/tinymce/js/tinymce/tinymce.min.js"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>编辑Blog</h5>
            </div>
            <div class="ibox-content">
{{--                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>--}}
                <a href="{{route('blogs.blog')}}"><button class="btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" style="width: 100%;overflow: auto;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title H1(文章名称,不允许出现特殊字符)：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="title_h1" value="{{$row->title_h1}}" required data-msg-required="请输入Title H1">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">category：</label>
                        <div class="input-group col-sm-1">
                            <select class="form-control" name="type_id">
                                @foreach ($types as $k=>$v)
                                    <option value="{{$k}}" @if($k==$row->type_id) selected @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tags：</label>
                        <div class="input-group col-sm-6">
                            @foreach ($tags as $k=>$v)
                                <label style="margin-bottom: 10px;margin-right: 60px;"><input class="required" type="checkbox" name="tags" value="{{$k}}" @if(in_array($k,explode(',',$row->tag_id))) checked="checked" @endif>&nbsp;&nbsp;{{$v}}</label>
                            @endforeach
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Title（不允许出现特殊字符）：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="title" value="{{$row->title}}" required data-msg-required="请输入Seo Title">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Description：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="description" value="{{$row->description}}" required data-msg-required="请输入Seo Description">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Keywords：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="keywords" value="{{$row->keywords}}" required data-msg-required="请输入Seo Keywords">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Slug(确保唯一性)：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="slug" value="{{$row->slug}}" required data-msg-required="请输入Slug">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">cover(封面图(大小不能超过5M,文件名必须是英文))：</label>
                        <div class="input-group col-sm-2">
                            <input type="file" class="form-control" name="cover">
                            <span class="view picview ">
                                <img id="thumbnail-avatar" class="thumbnail img-responsive" src="{{$row->cover}}" width="100" height="100">
                            </span>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort id(排序 从小到大)：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="sort_id" value="{{$row->sort_id}}" required data-msg-required="请输入Sort id" min="0" oninput="if(value.length>9)value=value.slice(0,9)">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Abstract(仅Categories选择为products时填写)：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="abstract" value="{{$row->abstract}}">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Content：</label>
                        <div class="input-group col-sm-2">
                            <textarea id="content" name="content" class="form-control" rows="5" cols="20">{{$row->content}}</textarea>
                        </div>
                    </div>
                    {{--                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>--}}
                    {{--                    <div class="form-group">--}}
                    {{--                        <label class="col-sm-2 control-label">所属角色：</label>--}}
                    {{--                        <div class="input-group col-sm-2">--}}
                    {{--                            @foreach($roles as $k=>$item)--}}
                    {{--                                <label><input type="checkbox" name="role_id[]" value="{{$item->id}}" @if($item->id == old('role_id')) checked="checked" @endif> {{$item->name}}</label><br/>--}}
                    {{--                            @endforeach--}}
                    {{--                            @if ($errors->has('role_id'))--}}
                    {{--                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('role_id')}}</span>--}}
                    {{--                            @endif--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button class="btn btn-primary" type="button" onclick="edit_blog()"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white reset" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        tinymce.init({
            selector: '#content',
            // skin:'oxide-dark',
            language: 'zh_CN',
            plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template code codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern help emoticons autosave bdmap indent2em formatpainter axupimgs',
            toolbar: 'code undo redo restoredraft | cut copy paste pastetext | forecolor backcolor bold italic underline strikethrough link anchor | alignleft aligncenter alignright alignjustify outdent indent | \
    formatselect fontselect fontsizeselect | bullist numlist | blockquote subscript superscript removeformat | \
    table image media charmap emoticons hr pagebreak insertdatetime print preview | fullscreen | bdmap indent2em lineheight formatpainter axupimgs',
            width: 1100,
            height: 750, //编辑器高度
            min_height: 300,
            toolbar_mode: 'sliding',
            images_upload_url: '/admin/blogs/editorUpload',
            images_upload_base_path: '',
            contextmenu: "paste | link image inserttable | cell row column deletetable",
            // content_css: [ //可设置编辑区内容展示的css，谨慎使用
            //     '/static/reset.css',
            //     '/static/ax.css',
            //     '/static/css.css',
            // ],
            fontsize_formats: '12px 14px 16px 18px 24px 36px 48px 56px 72px',
            font_formats: '微软雅黑=Microsoft YaHei,Helvetica Neue,PingFang SC,sans-serif;苹果苹方=PingFang SC,Microsoft YaHei,sans-serif;宋体=simsun,serif;仿宋体=FangSong,serif;黑体=SimHei,sans-serif;Helvetica=helvetica;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;',
            importcss_append: true,
            toolbar_sticky: false,
            relative_urls: false,
            remove_script_host: false,
            autosave_ask_before_unload: true,
            autosave_interval: '20s',
        });

        function edit_blog(){
            var serializeObj={};
            var array=$('#form_data').serializeArray();
            $(array).each(function(){
                if(serializeObj[this.name]){
                    if($.isArray(serializeObj[this.name])){
                        serializeObj[this.name].push(this.value);
                    }else{
                        serializeObj[this.name]=[serializeObj[this.name],this.value];
                    }
                }else{
                    if('content'==this.name){
                        serializeObj[this.name]=tinymce.editors[0].getContent();
                    }else {
                        serializeObj[this.name]=this.value;
                    }
                }
            });
            $.ajax({
                url: "{{route('blogs.blogUpdate',$row->id)}}",
                data: {_token: '{{ csrf_token() }}',data:serializeObj},
                type: 'post',
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    console.log(re)
                    if (re.code==200) {
                        layer.msg("修改blog成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            $(".reset").click();
                            $(".back").click();
                        });
                    } else {
                        //失败提示
                        layer.msg(re.msg, {
                            icon: 2,
                            time: 2000
                        });
                    }
                }
            });
        }
    </script>
@endsection