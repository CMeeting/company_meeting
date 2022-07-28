@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>添加管理员</h5>
        </div>
        <div class="ibox-content">
            <?php
            $groupidsjson=json_encode($groupids);
            $ruleidsjson=json_encode($ruleidsc);
            ?>
{{--            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>--}}
            <a href="{{route('admins.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <teleport id="groupidsjson" style="display: none">{{$groupidsjson}}</teleport>
            <teleport id="ruleidsjson"  style="display: none">{{$ruleidsjson}}</teleport>
                <form class="form-horizontal m-t-md" id="thisForm" action="{{ route('admins.update',$admin->id) }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}

                {{method_field('PATCH')}}

                <div class="form-group">
                    <label class="col-sm-2 control-label">用户名：</label>
                    <div class="input-group col-sm-2">
                        <input type="text" class="form-control" name="name" value="{{$admin->name}}" required data-msg-required="请输入用户名">
                        @if ($errors->has('name'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('name')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">密码：</label>
                    <div class="input-group col-sm-2">
                        <input type="password" class="form-control" name="password">
                        @if ($errors->has('password'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('password')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">头像：</label>
                    <div class="input-group col-sm-2">
                        <input type="file" class="form-control" name="avatr">
                        @if ($errors->has('avatr'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('avatr')}}</span>
                        @endif
                        <span class="view picview ">
                           <img id="thumbnail-avatar" class="thumbnail img-responsive" src="{{$admin->avatr}}" width="100" height="100">
                        </span>
                    </div>
                </div>
                <teleport style="display: none" id="testt123">{{$rolesarr}}</teleport>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" style="padding-top: 0">所属角色：</label>
                    <div class="input-group col-sm-10" style="display: flex; flex-wrap: wrap;">
                        @php
                            $ruleids = $admin->roles->pluck('id')->toArray();
                        @endphp

                        @foreach($roles as $k=>$item)
                            @if($item->id!=1)
                                @if(in_array(1,$groupids))
                                    <label style="margin-right: 10px;"><input type="checkbox" class="check" id="{{$item->id}}" name="role_id[]" value="{{$item->id}}"  onchange="dd()" @if(in_array($item->id,$ruleids)) checked="checked" @endif> {{$item->name}}</label><br/>
                                @else
                                    <label style="margin-right: 10px;"><input type="checkbox" class="check" id="{{$item->id}}" name="role_id[]" value="{{$item->id}}"  onchange="dd()" @if(!in_array($item->id,$groupids)) disabled @endif @if(in_array($item->id,$ruleids)) checked="checked" @endif> {{$item->name}}</label><br/>
                                @endif
                            @endif
                        @endforeach
                        @if ($errors->has('role_id'))
                            <label style="margin-right: 10px;"><span style="color: red;height: 10px;line-height: 10px" class="help-block m-b-none"><i class="fa fa-info-circle"></i>请先选择角色</span></label>
                        @endif

                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" style="padding-top: 0">拥有权限：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-left: 0">
                        <!-- /.循环一级权限数据 -->
                        @foreach($rolesinfo as $k=>$vo)
                            <div class="md-checkbox" style="margin-right:10px;">
                                <input type="checkbox" id="new_rules_{{$vo['id']}}" name="rules_id[]" value="{{$vo['id']}}"  class="md-check checkbox-parent" dataid="id-{{$vo['id']}}" @if(in_array($vo['id'],$adminroles)) checked @else disabled @endif/>
                                <label for="new_rules_{{$vo['id']}}">
                                    <span></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{$vo['name']}}</label>
                            </div>
                            @if(count($vo['sub'])>1)
                            <!-- /.循环二级权限数据 -->
                                @foreach($vo['sub'] as $ks=>$sub)
                                    <div class="md-checkbox" style="padding-left:30px; color:#333333">
                                        <input type="checkbox" id="new_rules_{{$sub['id']}}" name="rules_id[]" value="{{$sub['id']}}"  class="md-check checkbox-parent checkbox-child" dataid="id-{{$vo['id']}}-{{$sub['id']}}" @if(in_array($sub['id'],$adminroles)) checked @else disabled @endif/>
                                        <label for="new_rules_{{$sub['id']}}">
                                            <span></span>
                                            <span class="check"></span>
                                            <span class="box"></span> {{$sub['name']}}</label>
                                    </div>
                                    @if(count($sub['sub'])>1)
                                    <!-- /.循环三级权限数据 -->
                                        <div style="display: flex; flex-wrap: wrap; padding-left:60px;">
                                            @foreach($sub['sub'] as $kss=>$subb)
                                                <div class="md-checkbox" style="margin-right: 20px; margin-bottom: 10px; color:#666666">
                                                    <input type="checkbox" id="new_rules_{{$subb['id']}}" name="rules_id[]" value="{{$subb['id']}}"  class="md-check checkbox-parent checkbox-child"  dataid="id-{{$vo['id']}}-{{$sub['id']}}-{{$subb['id']}}" @if(in_array($subb['id'],$adminroles)) checked @else disabled @endif />
                                                    <label for="new_rules_{{$subb['id']}}">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span> {{$subb['name']}}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                <!-- 循环二级权限数据./ -->
                                @endforeach
                            @endif
                        <!-- 循环一级权限数据./ -->
                        @endforeach
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label">状态：</label>
                    <div class="input-group col-sm-1">
                        <select class="form-control" name="status">
                            <option value="1" @if($admin->status == 1) selected="selected" @endif>正常</option>
                            <option value="2" @if($admin->status == 2) selected="selected" @endif>锁定</option>
                        </select>
                        @if ($errors->has('status'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('status')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <div class="col-sm-12 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                        <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
</div>
@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script>
    var c="";
    var a = "";
    var b = "";
    var $categroys = "";
    var $groupidsjson = "";
    var $ruleidsjson = "";

    /* 权限配置	选中上级自动选中下级 */
    $(function () {
        a=$("#groupidsjson").text();
        b=$("#ruleidsjson").text();
        c=$("#testt123").text();
        $categroys = JSON.parse(c);
        $groupidsjson = JSON.parse(a);
        $ruleidsjson = JSON.parse(b);
        //判断是否已全选
        function isAllChecked(){
            var form = $('#thisForm')[0];
            var isChecked = true;

            for (var i = 0; i < form.elements.length; i++) {
                var e = form.elements[i];
                if (e.type == 'checkbox' && e.name != 'chkAll' && e.disabled == false) {
                    if (!e.checked) {
                        isChecked = false;
                        break;
                    }
                }
            }

            $('#chkAll').prop('checked', isChecked);
        }
        var rules = $('#rules').val();
        if(rules === 'all'){
            $('#chkAll').click();
        }else{
            isAllChecked();
        }

        //动态选择框，上下级选中状态变化
        $('input.checkbox-parent').on('change', function () {
            var dataid = $(this).attr("dataid");
            $('input[dataid^=' + dataid + '-]').prop('checked', $(this).is(':checked'));

            isAllChecked();
        });
        $('input.checkbox-child').on('change', function () {
            var dataid = $(this).attr("dataid");
            dataid = dataid.substring(0, dataid.lastIndexOf("-"));
            var parent = $('input[dataid=' + dataid + ']');
            if ($(this).is(':checked')) {
                parent.prop('checked', true);
                //循环到顶级
                while (dataid.lastIndexOf("-") != 2) {
                    dataid = dataid.substring(0, dataid.lastIndexOf("-"));
                    parent = $('input[dataid=' + dataid + ']');
                    parent.prop('checked', true);
                }
            } else {
                //父级
                if ($('input[dataid^=' + dataid + '-]:checked').length == 0) {
                    parent.prop('checked', false);
                    //循环到顶级
                    while (dataid.lastIndexOf("-") != 2) {
                        dataid = dataid.substring(0, dataid.lastIndexOf("-"));
                        parent = $('input[dataid=' + dataid + ']');
                        if ($('input[dataid^=' + dataid + '-]:checked').length == 0) {
                            parent.prop('checked', false);
                        }
                    }
                }
            }
        });
    });


    function dd(){
        $(".md-check").attr("disabled", true);
        $(".md-check").attr("checked", false);
        var index = $.inArray(1, $groupidsjson);
        $(".check").each(function (){
            if($(this).is(':checked')){
                var id=$(this).attr("id");
                for (var i=0;i<$categroys[id].length;i++){
                    if(index>=0){
                        $("#new_rules_"+$categroys[id][i]).prop("checked", true);
                        $("#new_rules_"+$categroys[id][i]).attr("disabled", false);
                    }else{
                        var pndex=$.inArray($categroys[id][i], $ruleidsjson)
                        if(pndex>=0){
                            $("#new_rules_"+$categroys[id][i]).prop("checked", true);
                            $("#new_rules_"+$categroys[id][i]).attr("disabled", false);
                        }
                    }
                }
            }
        })
        $("#new_rules_1").prop("checked", true);
        $("#new_rules_1").attr("disabled", true);
    }
</script>