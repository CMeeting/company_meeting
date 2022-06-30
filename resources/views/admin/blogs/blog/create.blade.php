@extends('admin.layouts.layout')
@section('content')
    <script src="/tinymce/tinymce.min.js"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加Blog</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('blogs.blog')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> Blog列表</button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" action="{{ route('blogs.blogStore') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title H1：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[title_h1]" required data-msg-required="请输入Title H1">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">category：</label>
                        <div class="input-group col-sm-1">
                            <select class="form-control" name="data[type_id]">
                                @foreach ($types as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tags：</label>
                        <div class="input-group col-sm-2">
                            @foreach ($tags as $k=>$v)
                            <label><input class="required" type="checkbox" name="tags[]" value="{{$k}}">{{$v}}</label>
                            @endforeach
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Title：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[title]" required data-msg-required="请输入Seo Title">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Description：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[description]" required data-msg-required="请输入Seo Description">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Seo Keywords：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[keywords]" required data-msg-required="请输入Seo Keywords">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Slug：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[slug]" required data-msg-required="请输入Slug">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">封面图：</label>
                        <div class="input-group col-sm-2">
                            <input type="file" class="form-control" name="cover">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort id：</label>
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="data[sort_id]" required data-msg-required="请输入Sort id">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Abstract：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[abstract]" required data-msg-required="请输入Abstract">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Content：</label>
                        <div class="input-group col-sm-2">
                            <textarea id="content" name="data[content]" class="form-control" rows="5" cols="20"></textarea>
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
                            <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
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
    images_upload_url: '/admin/changelogs/e_upload',
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
    </script>
@endsection