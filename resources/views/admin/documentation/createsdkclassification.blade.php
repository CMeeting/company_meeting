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

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>New SDK Guides</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" name="form"  method="post" action="{{route('documentation.createRunSdkclassification')}}" >
                    {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> order_num(排序 从大到小)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="displayorder"  type="number" class="form-control" name="data[displayorder]" min="1" max="99999999" oninput="if(value.length>8)value=value.slice(0,8)" value="999" required>
                                        <span class="lbl"></span>
                                    </div>
                                </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">title(分类名称)</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="name"  class="form-control" name="data[title]" required>
                            <span class="lbl"></span>
                        </div>
                    </div>

                    <textarea id="testt123" style="display: none">{{$parent}}</textarea>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> classification(上级分类)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <select name="data[pid]" class="form-control sels"  id="selectid" @if(isset($pid) && $pid) style="pointer-events: none;color: #9f9f9f" @endif>
                                            <option value="0">--默认一级分类--</option>
                                            @foreach($material as $vs)
                                                <option value="{{$vs['id']}}"
                                                        @if(isset($pid) && $pid==$vs['id'])
                                                        selected
                                                        @endif
                                                >{{$vs['lefthtml']}}{{$vs['title']}}
                                                    @if($vs['lvl']==1)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$vs['platforname']}}------------{{$vs['versionname']}}@endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @foreach($material as $vs)
                                        <span style="display: none" id="a_{{$vs['id']}}">{{$vs['platformid']}}</span>
                                        <span style="display: none" id="b_{{$vs['id']}}">{{$vs['version']}}</span>
                                        @endforeach
                                    </div>
                                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 平台/版本：</label>
                        <div class="col-sm-6 col-xs-12">
                            @if(isset($data['pid']) && $data['pid'])
                            <select autocomplete="off" class="fenlei form-control ccs" id="category_parent" name="data[platformid]" onchange="renderCategoryThirdbypcate(this.value)" onclick="renderCategoryThirdbypcate(this.value)" style="pointer-events: none;color: #9f9f9f">
                            </select>
                            <select autocomplete="off" class="fenlei form-control ccs" id="category_child" name="data[version]"  style="pointer-events: none;color: #9f9f9f;margin-left: 5px">
                            </select>
                            @else
                            <select autocomplete="off" class="fenlei form-control ccs" id="category_parent" name="data[platformid]" onchange="renderCategoryThirdbypcate(this.value)" onclick="renderCategoryThirdbypcate(this.value)">
                                <option value="0">请选择平台</option>
                            </select>
                            <select autocomplete="off" class="fenlei form-control ccs" id="category_child" name="data[version]"  style="display: none;margin-left: 5px">
                                <option value="0">请选择版本</option>
                            </select>
                            @endif
                        </div>
                    </div>




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

@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script type="text/javascript">
    var c='';
    var $categroys = '';
    var defaultpcate = '';
    var defaultccate = '';
    function chage_specialbysid(){
        $selectChild = $('#category_parent');
        $selectThird = $('#category_child');
        var html = '<option value="0">请选择平台</option>';
        var html1 = '<option value="0">请选择版本</option>';
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
        var html1 = '<option value="0">请选择版本</option>';
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
        var tishi=$('#selectid').find("option:selected").text();
        var tishival=$('#selectid').find("option:selected").val();
        if(tishival==0){
            $("#name").attr("placeholder","当前添加的是顶级分类名称");
        }else{
            tishi=tishi.substring(1);
            tishi = tishi.replace(/\s+/g,'');
            $("#name").attr("placeholder","当前添加的是"+tishi+"分类下级的分类名称");
        }
        $('#selectid').on('change', function () {
            tishi=$('#selectid').find("option:selected").text();
            tishival=$('#selectid').find("option:selected").val();
            if(tishival==0){
                $("#name").attr("placeholder","当前添加的是顶级分类名称");
                selectChilds.css("color","#000000");
                selectChilds.css("pointer-events","auto");
                selectThirds.css("color","#000000");
                selectThirds.css("pointer-events","auto");
                chage_specialbysid();
            }else{
                var platformid= $("#a_"+tishival).text();
                var version= $("#b_"+tishival).text();
                selectChilds = $('#category_parent');
                selectThirds = $('#category_child');
                var html = '<option value="'+platformid+'">已绑定上级分类平台数据</option>';
                var html1 = '<option value="'+version+'">已绑定上级分类版本数据</option>';
                selectChilds.html(html);
                selectThirds.html(html1);
                selectThirds.css("pointer-events","none");
                selectChilds.css("color","#9f9f9f");
                selectChilds.css("pointer-events","none");
                selectThirds.css("color","#9f9f9f");
                tishi=tishi.substring(1);
                tishi = tishi.replace(/\s+/g,'');
                $("#name").attr("placeholder","当前添加的是"+tishi+"分类下级的分类名称");
            }
        })

        c=$("#testt123").text();
        $categroys = JSON.parse(c);
        defaultpcate = parseInt("{{$data['platformid']}}");
        defaultccate = parseInt("{{$data['version']}}");
        $selectChild = $('#category_parent');
        chage_specialbysid();

        $("form").submit(function(e){
            if(($("#category_parent").val()==0 || $("#category_child").val()==0) && $("#selectid").val()==0){
                layer.msg("请选择平台/版本", {
                    icon: 2,
                    time: 2000
                });
                return false;
            }
        })
    })

</script>