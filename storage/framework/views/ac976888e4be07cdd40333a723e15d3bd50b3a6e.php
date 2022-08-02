<?php $__env->startSection('content'); ?>
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
            <?php
              $groupidsjson=json_encode($groupids);
              $ruleidsjson=json_encode($ruleids);
            ?>
            <div class="ibox-title">
                <h5>添加管理员</h5>
            </div>
            <div class="ibox-content">

                <a href="<?php echo e(route('admins.index')); ?>"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <teleport id="groupidsjson" style="display: none"><?php echo e($groupidsjson); ?></teleport>
                <teleport id="ruleidsjson"  style="display: none"><?php echo e($ruleidsjson); ?></teleport>
                <form class="form-horizontal" id="thisForm" action="<?php echo e(route('admins.store')); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">用户名：</label>
                        <div class="input-group col-sm-2">
                            <input type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" required data-msg-required="请输入用户名">
                            <?php if($errors->has('name')): ?>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('name')); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">密码：</label>
                        <div class="input-group col-sm-2">
                            <input type="password" class="form-control" name="password" required data-msg-required="请输入密码">
                            <?php if($errors->has('password')): ?>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('password')); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">头像：</label>
                        <div class="input-group col-sm-2">
                            <input type="file" class="form-control" name="avatr">
                            <?php if($errors->has('avatr')): ?>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('avatr')); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <teleport style="display: none" id="testt123"><?php echo e($rolesarr); ?></teleport>

                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top: 0">所属角色：</label>
                        <div class="input-group col-sm-10" style="display: flex; flex-wrap: wrap;">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($item->id!=1): ?>
                                    <?php if(in_array(1,$groupids)): ?>
                                <label style="margin-right: 10px;"><input type="checkbox" class="check" id="<?php echo e($item->id); ?>" name="role_id[]" value="<?php echo e($item->id); ?>"  onchange="dd()"> <?php echo e($item->name); ?></label><br/>
                                        <?php else: ?>
                                        <label style="margin-right: 10px;"><input type="checkbox" class="check" id="<?php echo e($item->id); ?>" name="role_id[]" value="<?php echo e($item->id); ?>"  onchange="dd()" <?php if(!in_array($item->id,$groupids)): ?> disabled <?php endif; ?>> <?php echo e($item->name); ?></label><br/>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($errors->has('role_id')): ?>
                                <div style="width: 100%;height: 30px">
                                    <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>请先添加角色</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top: 0">拥有权限：</label>
                        <div class="col-sm-6 col-xs-12" style="padding-left: 0">
                            <!-- /.循环一级权限数据 -->
                            <?php $__currentLoopData = $rolesinfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$vo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="md-checkbox" style="margin-right:10px;">
                                    <input type="checkbox" id="new_rules_<?php echo e($vo['id']); ?>" name="rules_id[]" value="<?php echo e($vo['id']); ?>"  class="md-check checkbox-parent" dataid="id-<?php echo e($vo['id']); ?>" disabled/>
                                    <label for="new_rules_<?php echo e($vo['id']); ?>">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span> <?php echo e($vo['name']); ?></label>
                                </div>
                                <?php if(count($vo['sub'])>1): ?>
                                <!-- /.循环二级权限数据 -->
                                <?php $__currentLoopData = $vo['sub']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ks=>$sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="md-checkbox" style="padding-left:30px; color:#333333">
                                        <input type="checkbox" id="new_rules_<?php echo e($sub['id']); ?>" name="rules_id[]" value="<?php echo e($sub['id']); ?>"  class="md-check checkbox-parent checkbox-child" dataid="id-<?php echo e($vo['id']); ?>-<?php echo e($sub['id']); ?>" disabled/>
                                        <label for="new_rules_<?php echo e($sub['id']); ?>">
                                            <span></span>
                                            <span class="check"></span>
                                            <span class="box"></span> <?php echo e($sub['name']); ?></label>
                                    </div>
                                    <?php if(count($sub['sub'])>1): ?>
                                <!-- /.循环三级权限数据 -->
                                    <div style="display: flex; flex-wrap: wrap; padding-left:60px;">
                                    <?php $__currentLoopData = $sub['sub']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kss=>$subb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="md-checkbox" style="margin-right: 20px; margin-bottom: 10px; color:#666666">
                                            <input type="checkbox" id="new_rules_<?php echo e($subb['id']); ?>" name="rules_id[]" value="<?php echo e($subb['id']); ?>"  class="md-check checkbox-parent checkbox-child"  dataid="id-<?php echo e($vo['id']); ?>-<?php echo e($sub['id']); ?>-<?php echo e($subb['id']); ?>" disabled/>
                                            <label for="new_rules_<?php echo e($subb['id']); ?>">
                                                <span></span>
                                                <span class="check"></span>
                                                <span class="box"></span> <?php echo e($subb['name']); ?></label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php endif; ?>

                                <!-- 循环二级权限数据./ -->
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                                <!-- 循环一级权限数据./ -->
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态：</label>
                        <div class="input-group col-sm-1">
                            <select class="form-control" name="status">
                                <option value="1" <?php if(old('status') == 1): ?> selected="selected" <?php endif; ?>>正常</option>
                                <option value="2" <?php if(old('status') == 2): ?> selected="selected" <?php endif; ?>>锁定</option>
                            </select>
                            <?php if($errors->has('status')): ?>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('status')); ?></span>
                            <?php endif; ?>
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
<?php $__env->stopSection(); ?>
<script src="<?php echo e(loadEdition('/js/jquery.min.js')); ?>"></script>
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
    }
</script>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>