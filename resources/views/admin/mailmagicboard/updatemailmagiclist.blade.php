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
                <h5>Edit Mailmagicboard</h5>
            </div>

            <div class="ibox-content">
                <a href="{{route('mailmagicboard.mailmagic_list')}}" style="margin-bottom: 8px">
                    <button class="menuid btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i> 返回列表
                    </button>
                </a>

                <form class="form-horizontal" id="forms" name="form"  method="post" action="{{route('mailmagicboard.updaterunmailmagiclist')}}" >
                    {{ csrf_field() }}
                    <div class="form-group" style="margin-top: 10px">
                            邮件内容替换规则(在邮件内容用变量代替内容，实际发送时会自动获取变量名代表的内容)：#@username(客户名称) #@phone(手机号) #@code(序列码) #@paytime(订单支付时间/单号创建时间) #@mail(邮件地址) #@product(产品名称)
                    </div>
                    <input type="hidden" name="data[id]" value="{{$data['id']}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> name(模板名称)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="name"  class="form-control" name="data[name]" value="{{$data['name']}}" required maxlength="255">
                            <span class="lbl"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 邮件标题：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="title"  class="form-control" name="data[title]" value="{{$data['title']}}" required maxlength="255">
                            <span class="lbl"></span>
                        </div>
                    </div>

                    <div class="form-group tinyeditor">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> content(邮件内容)：</label>
                        <div class="col-sm-10 col-xs-12">
                            <textarea id="content" name="data[info]" class="form-control" rows="5" cols="20">{{$data['info']}}</textarea>
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
        tinymce.init({
            selector: '#content',
            // skin:'oxide-dark',
            language: 'zh_CN',
            plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template code codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern help emoticons autosave bdmap indent2em axupimgs',
            toolbar: 'code undo redo restoredraft | cut copy paste pastetext | forecolor backcolor bold italic underline strikethrough link anchor | alignleft aligncenter alignright alignjustify outdent indent | \
    formatselect fontselect fontsizeselect | bullist numlist | blockquote subscript superscript removeformat | \
    table image media charmap emoticons hr pagebreak insertdatetime print preview | fullscreen | bdmap indent2em lineheight axupimgs',
            width: '100%',
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
            // lineheight_formats: "8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
            lineheight_formats: "1 2 3 4 5 6",
            fontsize_formats: '12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 48px 56px 72px',
            font_formats: '微软雅黑=Microsoft YaHei,Helvetica Neue,PingFang SC,sans-serif;苹果苹方=PingFang SC,Microsoft YaHei,sans-serif;宋体=simsun,serif;仿宋体=FangSong,serif;黑体=SimHei,sans-serif;Helvetica=helvetica;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;',
            importcss_append: true,
            toolbar_sticky: false,
            relative_urls: false,
            remove_script_host: false,
            autosave_ask_before_unload: true,
            autosave_interval: '5s',
        });
        function submits(){
            var form_data = new FormData($("#forms")[0]);
            form_data.set("data[info]",tinymce.editors[0].getContent());
            layer.close(index);
            var index = layer.load();
            $.ajax({
                url:"{{route('mailmagicboard.updaterunmailmagiclist')}}",
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                type:"post",
                data: form_data,
                success:function(data){
                    if(data.code==1){
                        layer.close(index);
                        layer.msg("修改成功", {time: 1500, anim: 1});
                        $(".reset").click();
                        $(".back").click();
                    }else{
                        layer.close(index);
                        layer.msg(data.msg, {time: 1500, anim: 6});
                        return false;
                    }
                }
            })


        }
    </script>
@endsection
