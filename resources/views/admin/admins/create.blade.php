@extends('admin.layouts.layout')
@section('content')
    <style>
        .md-checkbox-inline .md-checkbox{
            line-height: normal !important;
        }
        .md-checkbox>label{
            margin-bottom: 0;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>添加管理员</h5>
            </div>
            <div class="ibox-content">
                <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                <a href="{{route('admins.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 管理员管理</button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <form class="form-horizontal" id="thisForm" action="{{ route('admins.store') }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">用户名：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="{{old('name')}}" required data-msg-required="请输入用户名">
                            @if ($errors->has('name'))
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('name')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">密码：</label>
                        <div class="input-group col-sm-2">
                            <input type="password" class="form-control" name="password" required data-msg-required="请输入密码">
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
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top: 0">所属角色：</label>
                        <div class="input-group col-sm-10" style="display: flex; flex-wrap: wrap;">
                            @foreach($roles as $k=>$item)
                                <label style="margin-right: 10px;"><input type="checkbox" name="role_id[]" value="{{$item->id}}" @if($item->id == old('role_id')) checked="checked" @endif> {{$item->name}}</label><br/>
                            @endforeach
                            @if ($errors->has('role_id'))
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('role_id')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">拥有权限：</label>
                        <div class="col-sm-6 col-xs-12">
                            <!-- /.循环一级权限数据 -->
                            @foreach($rolesinfo as $k=>$vo)
                                <div class="md-checkbox" style="margin-right:10px;">
                                    <input type="checkbox" id="new_rules_{{$vo['id']}}" name="role_id[]" value="{{$vo['id']}}"  class="md-check checkbox-parent" dataid="id-{{$vo['id']}}" />
                                    <label for="new_rules_{{$vo['id']}}">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span> {{$vo['name']}}</label>
                                </div>
                                @if(count($vo['sub'])>1)
                                <!-- /.循环二级权限数据 -->
                                @foreach($vo['sub'] as $ks=>$sub)
                                    <div class="md-checkbox" style="padding-left:30px; color:#333333">
                                        <input type="checkbox" id="new_rules_{{$sub['id']}}" name="role_id[]" value="{{$sub['id']}}"  class="md-check checkbox-parent checkbox-child" dataid="id-{{$vo['id']}}-{{$sub['id']}}" />
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
                                            <input type="checkbox" id="new_rules_{{$subb['id']}}" name="role_id[]" value="{{$subb['id']}}"  class="md-check checkbox-parent checkbox-child"  dataid="id-{{$vo['id']}}-{{$sub['id']}}-{{$subb['id']}}" />
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
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态：</label>
                        <div class="input-group col-sm-1">
                            <select class="form-control" name="status">
                                <option value="1" @if(old('status') == 1) selected="selected" @endif>正常</option>
                                <option value="2" @if(old('status') == 2) selected="selected" @endif>锁定</option>
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
@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script>
    /* 权限配置	选中上级自动选中下级 */
    $(function () {
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

            isAllChecked();
        });
    });
</script>