@extends('admin.layouts.layout')
@section('content')
    <style>
        dl.layui-anim.layui-anim-upbit {
            z-index: 1000;
        }
        .sels{
            display: inline-block;
            width: calc(88.5% - 22px);
            border: 1px solid #c9d0d6;
            border-radius: 3px;
            font-size: 0.95em;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            outline: none;
            padding: 8px 10px 7px;
        }
        .ccs{
            width: calc(100%);
        }

    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>New Platform&Version</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" name="form"  method="post" action="{{route('documentation.createRunPlatformVersion')}}" >
                    {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> order_num(排序 从大到小)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="displayorder"  type="number" class="form-control" name="data[displayorder]" min="1" max="99999999" oninput="if(value.length>8)value=value.slice(0,8)" required>
                                        <span class="lbl"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> classification(上级分类)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <select name="data[pid]" class="form-control ccs"  id="selectid" @if(isset($pid) && $pid) style="pointer-events: none;color: #9f9f9f" @endif>
                                            <option value="0">--默认一级分类--</option>
                                            @foreach($material as $vs)
                                                <option value="{{$vs['id']}}"
                                                        @if(isset($pid) && $pid==$vs['id'])
                                                        selected
                                                        @endif
                                                >{{$vs['lefthtml']}}{{$vs['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> name(平台或版本名称)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="name"  class="form-control" name="data[name]" required onKeyUp="value=value.replace(/[^\w\.\/-]/ig,'')">
                                        <span class="lbl"></span>
                                    </div>
                                </div>


                                @if(isset($pid) && $pid == 0)

                                    <div class="form-group h1title">
                                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> SEO title：</label>
                                        <div class="col-sm-6 col-xs-12">
                                            <input id="seotitel"  class="form-control  seotitel" name="data[seotitel]" required placeholder="SEO title只会绑定在平台数据">
                                            <span class="lbl"></span>
                                        </div>
                                    </div>
                                    <div class="form-group h1title">
                                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> H1 title：</label>
                                        <div class="col-sm-6 col-xs-12">
                                            <input id="h1title"  class="form-control " name="data[h1title]" required placeholder="H1 title只会绑定在平台数据">
                                            <span class="lbl"></span>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> enabled(是否显示)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="radio" name="data[enabled]" value="1" checked >显示
                                        <input type="radio" name="data[enabled]" value="0">隐藏
                                    </div>
                                </div>
                            </ol>

                            <div class="editor"></div>
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

@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script type="text/javascript">
        $(function (){
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
            });
            var tishi=$('#selectid').find("option:selected").text();
            var tishival=$('#selectid').find("option:selected").val();
            if(tishival==0){
                $("#name").attr("placeholder","当前添加的是平台名称");
            }else{
                tishi=tishi.substring(1);
                $("#name").attr("placeholder","当前添加的是"+tishi+"下的版本名称");
            }
            $('#selectid').on('change', function () {
                tishi=$('#selectid').find("option:selected").text();
                tishival=$('#selectid').find("option:selected").val();
                if(tishival==0){
                    $(".seotitel").show();
                    $(".h1title").show();
                    $("#seotitel").prop('required',true);
                    $("#h1title").prop('required',true);
                    $("#name").attr("placeholder","当前添加的是平台名称");
                }else{
                    $(".seotitel").hide();
                    $(".h1title").hide();
                    $("#seotitel").prop('required',false);
                    $("#h1title").prop('required',false);
                    tishi=tishi.substring(1);
                    $("#name").attr("placeholder","当前添加的是"+tishi+"下的版本名称");
                }
            })
        })
    </script>
