<?php $__env->startSection('content'); ?>
    <style>

        .handless span {

            display: block;
            position: absolute;
            left: 0;
            top: 4px;
            width: 100%;
            text-align: center;
            text-indent: 0;
            color: #fff;
            font-size: 20px;
            font-weight: normal;
        }
        .handless {
            position: absolute;
            margin: 0;
            left: 0;
            top: 0;
            cursor: pointer;
            width: 40px;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 1px solid #aaa;
            background: #ddd;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .abutton{
            display: inline-block;
            border-radius: 10px;
            border: 1px solid lavenderblush;
            margin-right: 3px;
            padding: 0px 5px 0px 5px;
            text-decoration:none;
            color: #f6fff8;
        }
        .cloros{
            background-color: #20e281;
        }
        .cloros1{
            background-color: #e2322d;
        }
        .cloros2{
            background-color: #0b94ea;
        }
        .cloros3{
            background-color: #7f3fe2;
        }
        .cloros4{
            background-color: red;
        }
        #cc{
            display: inline-block;width: 200px;background: #0b94ea;color: aliceblue;border-radius: 15px
        }
    </style>
    <div class="row" id="onType">

        <div class="col-md-12">

            <div class="portlet light ">
                <div class="portlet-title tabbable-line">
                    <div class="actions">
                        <a id="cc" class="addClassify btn btn-md  blue  dropdown-toggle mt-ladda-btn ladda-button" data-style="zoom-in"  type="button" href="<?php echo e(route('documentation.createPlatformVersion')); ?>">
                            <i class="fa fa-plus-circle"></i> New Platform&Version
                        </a>
                    </div>

                </div>
                <div class="port-body">
                    <div class="dd" id="nestable_list_3">
                        <div class="layui-fluid" style="min-width: 100%;">
                            <div class="layui-row layui-col-space15">
                                <div class="layui-col-md12"  style="min-height: 500px;overflow-y: auto">

                                    <div class="layui-card">
                                        <div class="layui-card-body layui-table-body layui-table-main"
                                             style="min-height: 450px;">
                                            <div class="port-body">

                                                <div class="dd" id="nestable_list_3">
                                                    <ol class="dd-list">
                                                        <?php if(count($cateList)>0): ?>
                                                        <?php $__currentLoopData = $cateList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="dd-item dd3-item item_<?php echo e($v['id']); ?>" data-id="<?php echo e($v['id']); ?>" id="classSecond_{$v['id']}">
                                                            <div class="dd-handle handless" onclick="zijishow('<?php echo e($v['id']); ?>')"><span id="jiantou_<?php echo e($v['id']); ?>">▽</span></div>
                                                            <div class="dd3-content">
                                                                <?php echo e($v['name']); ?><span class=" numbid_<?php echo e($v['id']); ?>">&nbsp;&nbsp;<font  style="font-size: 1em">排序</font>:[<?php echo e($v['displayorder']); ?>]</span>

                                                                <div class="item_edt_del">
                                                                    <font class="open_{$v.id}">
                                                                        <?php if($v['enabled'] == 1): ?>
                                                                        <a  data-id="{$v.id}" style="text-decoration: none"  class="openBtn_{$v.id} abutton cloros" data-style="zoom-out" onclick="show(<?php echo e($v['id']); ?>);">
                                                                            <span class="ladda-label">show</span>
                                                                        </a>
                                                                        <?php else: ?>
                                                                        <a data-id="{$v.id}" style="text-decoration: none" class="openBtn_{$v.id} abutton cloros1" data-style="zoom-out" onclick="show(<?php echo e($v['id']); ?>);">
                                                                            <span class="ladda-label">hide</span>
                                                                        </a>
                                                                        <?php endif; ?>
                                                                    </font>
                                                                    <a class="abutton cloros2" style="text-decoration: none"  href="<?php echo e(route('documentation.createPlatformVersion',$v['id'])); ?>">
                                                                        <i class="fa fa-plus-circle "></i> add
                                                                    </a>
                                                                    <a class="edit_{$v.id} abutton cloros3" style="text-decoration: none" href="<?php echo e(route('documentation.updatePlatformVersion',$v['id'])); ?>">
                                                                        <i class="fa fa-edit"></i> edit
                                                                    </a>

                                                                    <a onclick="del('<?php echo e($v['id']); ?>')" class="abutton cloros4" style="text-decoration: none">
                                                                        <i class="fa fa-trash-o fa-delete"></i> del
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            <?php if(isset($childCateList[$v['id']])): ?>
                                                            <ol class="dd-list">
                                                                <?php $__currentLoopData = $childCateList[$v['id']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <li class="dd-item dd3-item ziji_<?php echo e($v['id']); ?>" data-id="<?php echo e($vv['id']); ?>" parentid="<?php echo e($vv['pid']); ?>" id="classSecond_<?php echo e($vv['id']); ?>">
                                                                    <div class="dd-handle dd3-handle"></div>
                                                                    <div class="dd3-content">
                                                                        <?php echo e($vv['name']); ?><span class=" numbid_<?php echo e($vv['id']); ?>">&nbsp;&nbsp;排序:[<?php echo e($vv['displayorder']); ?>]</span>

                                                                        <div class="item_edt_del">
                                                                            <font class="open_<?php echo e($vv['id']); ?>">
                                                                                <?php if($vv['enabled'] == 1): ?>
                                                                                <a  data-id="<?php echo e($vv['id']); ?>"  class="openBtn_<?php echo e($vv['id']); ?> abutton cloros" data-style="zoom-out" onclick="show(<?php echo e($vv['id']); ?>);">
                                                                                    <span class="ladda-label">show</span>
                                                                                </a>
                                                                                <?php else: ?>
                                                                                <a data-id="<?php echo e($vv['id']); ?>"  class="openBtn_<?php echo e($vv['id']); ?> abutton cloros1" data-style="zoom-out" onclick="show(<?php echo e($vv['id']); ?>);">
                                                                                    <span class="ladda-label">hide</span>
                                                                                </a>
                                                                                <?php endif; ?>
                                                                            </font>

                                                                            <a class="edit_<?php echo e($vv['id']); ?> abutton cloros3"  href="<?php echo e(route('documentation.updatePlatformVersion',$vv['id'])); ?>">
                                                                                <i class="fa fa-edit"></i> edit
                                                                            </a>

                                                                            <a onclick="del('<?php echo e($vv['id']); ?>')" class="abutton cloros4">
                                                                                <i class="fa fa-trash-o fa-delete"></i> del
                                                                            </a>
                                                                        </div>

                                                                    </div>
                                                                </li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </ol> <?php endif; ?>
                                                        </li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php else: ?>
                                                        <div style="height: 300px; width: 100%; text-align: center; padding-top: 130px;">
                                                            <div>暂无数据</div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="" style="width: 100%;height: 50px;"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<script>
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消']
        }, function(){
            layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "<?php echo e(route('documentation.delPlatformVersion')); ?>",
                data: {delid:id, _token: '<?php echo e(csrf_token()); ?>'},
                type: 'post',
                dataType: "json",
                success: function (resp) {
                    // layer.close(index);
                    //成功提示
                    if (resp.code==0) {
                        layer.msg("删除成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            location.reload();
                        });
                    } else {
                        //失败提示
                        layer.msg(resp.msg, {
                            icon: 2,
                            time: 2000
                        });
                    }
                }
            });
        }, function(index){
            layer.close(index);
        });
    }
    function show(id){
        var index = layer.load();
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
            url: "<?php echo e(route('documentation.showHideclassification')); ?>",
            data: {id:id,type:'platform_version', _token: '<?php echo e(csrf_token()); ?>'},
            type: 'post',
            dataType: "json",
            success: function (resp) {
                if (resp.code==0) {
                    location.reload();
                } else {
                    //失败提示
                    layer.msg(resp.msg, {
                        icon: 2,
                        time: 2000
                    });
                }
            }
        });
    }
    function zijishow(id){

        if($(".ziji_"+id).is(':hidden')){
            $(".ziji_"+id).slideDown(100,"linear");
            $("#jiantou_"+id).text("▽");

        }else{
            $(".ziji_"+id).slideUp(100);
            $("#jiantou_"+id).text("▷");
        }
    }
</script>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>