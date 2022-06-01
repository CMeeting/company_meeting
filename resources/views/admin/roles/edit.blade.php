@extends('admin.layouts.layout')
@section('content')
<script src="/tinymce/tinymce.min.js"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>添加角色</h5>
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="{{route('roles.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 角色管理</button></a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <form class="form-horizontal m-t-md" action="{{route('roles.update',$role->id)}}" method="post">
                {!! csrf_field() !!}
                {{method_field('PATCH')}}
                <div class="form-group">
                    <label class="col-sm-2 control-label">角色名称：</label>
                    <div class="input-group col-sm-2">
                        <input type="text" class="form-control" name="name" value="{{$role->name}}" required data-msg-required="请输入角色名称">
                        @if ($errors->has('name'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('name')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">角色描述：</label>
                    <div class="input-group col-sm-3">
                        <textarea name="remark" class="form-control" rows="5" cols="20" data-msg-required="请输入角色描述">{{$role->remark}}</textarea>
                        @if ($errors->has('remark'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('remark')}}</span>
                        @endif
                    </div>
                </div>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">角色描述：</label>
                    <div class="input-group col-sm-3">
                        <textarea id="info" name="info" class="form-control" rows="5" cols="20">{{$role->remark}}</textarea>
                    </div>
                </div>

                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">排序：</label>
                    <div class="input-group col-sm-1">
                        <input type="text" class="form-control" name="order" value="{{$role->order}}" required data-msg-required="请输入排序">
                        @if ($errors->has('order'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('order')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态：</label>
                    <div class="input-group col-sm-1">
                        <select class="form-control" name="status">
                            <option value="1" @if($role->status == 1) selected="selected" @endif>启用</option>
                            <option value="2" @if($role->status == 2) selected="selected" @endif>禁用</option>
                        </select>
                        @if ($errors->has('status'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('status')}}</span>
                        @endif
                    </div>
                </div>
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
        selector: '#info',
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