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
    <script src="/tinymce/js/tinymce/tinymce.min.js"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加 Changelogs</h5>
            </div>
            <div class="ibox-content">
                <a href="{{route('changelogs.list')}}"><button class="btn btn-primary btn-sm back" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" style="width: 100%;overflow: auto;">
                    {!! csrf_field() !!}

                    <textarea id="testt123" style="display: none">{{$parent}}</textarea>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Platform/Products：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <select autocomplete="off" class="fenlei form-control ccs" id="category_parent" name="data[platform]" onchange="renderCategoryThirdbypcate(this.value)" onclick="renderCategoryThirdbypcate(this.value)">
                                <option value="0">请选择平台</option>
                            </select>
                            <select autocomplete="off" class="fenlei form-control ccs" id="category_child" name="data[product]"  style="margin-left: 5px">
                                <option value="0">请选择产品</option>
                            </select>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Development Language：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <select class="form-control" name="data[development_language]" id="development_language">
                                @foreach ($development_language as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Version_No（版本号，例如：1.1.1）</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[version_no]" value="" required data-msg-required="请输入标题">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Release：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <a id="release"><i class="fa fa-plus-circle"></i></a>
                        </div>
                    </div>
                    <div id="tables"></div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Slug(确保唯一性)：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[slug]" value="" onKeyUp="value=value.replace(/[^\w\.\/-]/ig,'')" required data-msg-required="请输入slug">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Seo Title（不允许出现特殊字符）：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="data[seo_title]" required data-msg-required="请输入Seo Title">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Seo Description：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <textarea class="form-control" name="data[seo_description]"></textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Change Date：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input id="change_date" type="text" class="form-control" name="data[change_date]" required data-msg-required="请输入Seo Keywords">
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">order_num(排序 从小到大)：</span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="number" class="form-control" name="data[order_num]" value="" required data-msg-required="请输入Sort id" min="0" oninput="if(value.length>9)value=value.slice(0,9)">
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -10px;position: absolute;color:black">Content:</span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <textarea id="content" name="data[content]" class="form-control" rows="5" cols="10"></textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button id="add_changelogs" class="btn btn-primary" type="button"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white reset" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        var arr=[];
        tinymce.init({
            selector: '#content',
            // skin:'oxide-dark',
            language: 'zh_CN',
            plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template code codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern help emoticons autosave bdmap indent2em axupimgs',
            toolbar: 'code undo redo restoredraft | cut copy paste pastetext | forecolor backcolor bold italic underline strikethrough link anchor | alignleft aligncenter alignright alignjustify outdent indent | \
    formatselect fontselect fontsizeselect | bullist numlist | blockquote subscript superscript removeformat | \
    table image media charmap emoticons hr pagebreak insertdatetime print preview | fullscreen | bdmap indent2em lineheight axupimgs',
            width: 1100,
            height: 750, //编辑器高度
            min_height: 300,
            toolbar_mode: 'sliding',
            images_upload_url: '{{ route('editorUpload') }}',
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

        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //执行一个laydate实例
           laydate.render({
                elem: '#change_date', //指定元素
                max:30,//日期最大值
                trigger: 'click',
                type: 'datetime',//日期时间选择器
            });
        });

        $("#add_changelogs").click(function () {
            var form_data = new FormData($("#form_data")[0]);
            form_data.set("data[content]",tinymce.editors[0].getContent());
            form_data.set("support",arr);
            $.ajax({
                url: "{{route('changelogs.store')}}",
                data: form_data,
                type: 'post',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    console.log(re)
                    if (re.code==200) {
                        layer.msg("添加Changelogs成功", {
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

        $("#release").click(function () {
            var platform=$("#category_parent").val();
            var product=$("#category_child").val();
            var development_language=$("#development_language").val();
            if(platform==0 || product==0){
                layer.msg("请选择Platform/Products后添加Release", {
                    icon: 2,
                    time: 2000
                });
                return false;
            }
            $.ajax({
                url: "{{route('changelogs.getsupport')}}",
                data: {
                    'platform':platform,
                    'product':product,
                    'development_language':development_language,
                    _token: '{{ csrf_token() }}'
                },
                type: 'post',
                dataType: "json",
                success: function (re) {
                   html='<div class="form-group" style="padding-left: 18px;"><table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;"><thead></thead><tbody>';
                    if (re.code==1) {
                        layer.open({
                            type: 1,
                            title: false,
                            closeBtn: 1, //不显示关闭按钮
                            shade: [0],
                            area: ['95%', '70%'],
                            anim: 2,
                            content: re.data,
                            // content: "<pre>"+data+"</pre>"
                            btn:['确定'],
                            btn1: function (index,layero) {
                                  $(".class").each(function (){

                                     arr.push($(this).val());
                                     html+="<tr>";
                                     html+='<td>'+$("#td1_"+$(this).val()).text()+'</td>';
                                     html+='<td>'+$("#td2_"+$(this).val()).text()+'</td>';
                                     html+='<td>'+$("#td3_"+$(this).val()).text()+'</td>';
                                     html+='<td>'+$("#td4_"+$(this).val()).text()+'</td>';
                                     html+='<td>'+$("#td5_"+$(this).val()).text()+'</td>';
                                     html+="</tr>";
                                  })
                                html+='</tbody></table></div>';
                                  $("#tables").html(html);
                            }
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
        $("#add_support").click(function () {
            var form_data = new FormData($("#form_data")[0]);
            // form_data.set("data[content]",tinymce.editors[0].getContent());

        })

        $(function (){
            var  selectChilds = $('#category_parent');
            var  selectThirds = $('#category_child');
            c=$("#testt123").text();
            $categroys = JSON.parse(c);
            $selectChild = $('#category_parent');
            chage_specialbysid();

            $("#category_parent").change(function (){
                arr=[];
                $("#tables").html('');
            })
            $("#category_child").change(function (){
                arr=[];
                $("#tables").html('');
            })
            $("#development_language").change(function (){
                arr=[];
                $("#tables").html('');
            })
        })
    </script>
@endsection