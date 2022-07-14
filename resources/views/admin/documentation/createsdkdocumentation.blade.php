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
                <h5>New Sdk Documentation</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" name="form"  method="post" action="{{route('documentation.createRunsdkDocumentation')}}" >
                    {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> order_num(排序 从大到小)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="displayorder"  type="number" class="form-control" name="data[displayorder]" min="1" max="99999999" oninput="if(value.length>8)value=value.slice(0,8)" value="1" required>
                                        <span class="lbl"></span>
                                    </div>
                                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Title H1(文章名称)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="name"  class="form-control" name="data[titel]" required>
                            <span class="lbl"></span>
                        </div>
                    </div>

                    <div class="form-group h1title">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> SEO Title：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="seotitel"  class="form-control  seotitel" name="data[seotitel]" required>
                            <span class="lbl"></span>
                        </div>
                    </div>
                    <div class="form-group h1title">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Slug(确保唯一性)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="Slug"  class="form-control " name="data[slug]" required onKeyUp="value=value.replace(/[^\w\.\/-]/ig,'')">
                            <span class="lbl"></span>
                        </div>
                    </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> classification(文章分类)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <select name="data[classification_ids]" class="form-control ccs"  id="classification_ids" @if(isset($classification_ids) && $classification_ids) style="pointer-events: none;color: #9f9f9f" @endif>
                                            <option value="0">--请选择文章分类--</option>
                                            @foreach($material as $vs)
                                                <option value="{{$vs['id']}}"
                                                        @if(isset($classification_ids) && $classification_ids==$vs['id'])
                                                        selected
                                                        @endif
                                                >{{$vs['lefthtml']}}{{$vs['title']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                    @if(isset($classification_ids) && $classification_ids)
                     <input type="hidden" name="type" value="1">
                    @endif
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">编辑器：</label>
                        <div class="col-sm-6 col-xs-12">
                            <select class="form-control" name="data[type]" id="type">
                                    <option value="1">富文本编辑器</option>
                                    <option value="2">MarkDown编辑器</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group tinyeditor">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> content：</label>
                        <div class="col-sm-10 col-xs-12">
                            <textarea id="content" name="data[info]" class="form-control" rows="5" cols="20"></textarea>
                        </div>
                    </div>
                    <div class="editor mdeditor hidden"></div>
                    <input type="hidden" name="data[info]" id="md_editor">

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> enabled(是否显示)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="radio" name="data[enabled]" value="1" checked >显示
                                        <input type="radio" name="data[enabled]" value="0">隐藏
                                    </div>
                                </div>


                            <div class="clearfix form-actions">
                                <div class="col-md-offset-3 col-md-9">

                                    <button class="btn dropdown-toggle ladda-button" type="submit" id="classifySubmitss"  style="background: deepskyblue" data-style="zoom-in">
                                        保&nbsp;&nbsp;存
                                    </button>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
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
        const zhHans = {
            "bold": "粗体",
            "boldText": "粗体文本",
            "cheatsheet": "Markdown 语法",
            "closeHelp": "关闭帮助",
            "closeToc": "关闭目录",
            "code": "代码",
            "codeBlock": "代码块",
            "codeLang": "编程语言",
            "codeText": "代码",
            "exitFullscreen": "退出全屏",
            "exitPreviewOnly": "恢复默认",
            "exitWriteOnly": "恢复默认",
            "fullscreen": "全屏",
            "h1": "一级标题",
            "h2": "二级标题",
            "h3": "三级标题",
            "h4": "四级标题",
            "h5": "五级标题",
            "h6": "六级标题",
            "headingText": "标题",
            "help": "帮助",
            "hr": "分割线",
            "image": "图片",
            "imageAlt": "alt",
            "imageTitle": "图片描述",
            "italic": "斜体",
            "italicText": "斜体文本",
            "limited": "已达最大字符数限制",
            "lines": "行数",
            "link": "链接",
            "linkText": "链接描述",
            "ol": "有序列表",
            "olItem": "项目",
            "preview": "预览",
            "previewOnly": "仅预览区",
            "quote": "引用",
            "quotedText": "引用文本",
            "shortcuts": "快捷键",
            "source": "源代码",
            "sync": "同步滚动",
            "toc": "目录",
            "top": "回到顶部",
            "ul": "无序列表",
            "ulItem": "项目",
            "words": "字数",
            "write": "编辑",
            "writeOnly": "仅编辑区",
            "strike": "删除线",
            "strikeText": "文本",
            "table": "表格",
            "tableHeading": "标题",
            "task": "任务列表",
            "taskText": "待办事项"
        }
        var plugins = [bytemdPluginGfm({
            locale: zhHans
        }), bytemdPluginHighlight(), bytemdPluginGemoji()];
        const $el = document.querySelector('.editor')
        console.log($el)
        var editor = new bytemd.Editor({
            target: $el,
            props: {
                plugins: plugins,
                locale: zhHans
            }
        });
        editor.$on('change', function (e) {
            editor.$set({ value: e.detail.value });
            console.log(e.detail.value)
            $("#md_editor").val(e.detail.value);
        });
        $("#type").change(function(){
            var v = $(this).val();
            if(1==v){
                $(".mdeditor").addClass('hidden');
                $("#md_editor").val("");
                editor.$set({ value: '' });
                $("#md_editor").removeAttr("name");
                $(".tinyeditor").removeClass('hidden');
            }else if(2==v){
                $(".tinyeditor").addClass('hidden');
                $("#content").val("");
                tinymce.editors[0].setContent("");
                $(".mdeditor").removeClass('hidden');
            }else{
                $(".mdeditor").addClass('hidden');
                $("#md_editor").val("");
                editor.$set({ value: '' });
                $("#md_editor").removeAttr("name");
                $(".tinyeditor").removeClass('hidden');
            }
        });
    </script>
@endsection
