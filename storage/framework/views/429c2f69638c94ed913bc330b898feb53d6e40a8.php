<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>角色管理</h5>
        </div>
        <div class="ibox-content">

            <a href="<?php echo e(route('roles.create')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加角色</button></a>
            <table class="table table-striped table-bordered table-hover m-t-md">
                <thead>
                <tr>
                    <th class="text-center" width="100">ID</th>
                    <th>角色名称</th>
                    <th>角色描述</th>
                    <th class="text-center" width="100">排序</th>

                    <th class="text-center" width="300">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td  class="text-center" ><?php echo e($item->id); ?></td>
                        <td><?php echo e($item->name); ?></td>
                        <td><?php echo e($item->remark); ?></td>
                        <td class="text-center"><?php echo e($item->order); ?></td>







                        <td class="text-center">
                            <div class="btn-group">
                                <?php if($item->id ==1): ?>
                                <?php else: ?>
                                    <a href="<?php echo e(route('roles.access',$item->id)); ?>"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 权限设置</button></a>
                                    <a href="<?php echo e(route('roles.edit',$item->id)); ?>"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                    <a onclick="del('<?php echo e($item->id); ?>')"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button>





                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php echo e($roles->links()); ?>

        </div>
    </div>
    <div class="clearfix"></div>
</div>
<script>
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消']
        }, function(){
            // layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "<?php echo e(route('roles.delete')); ?>",
                data: {id: id},
                type: 'get',
                // dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    //成功提示
                    if (resp.code==0) {
                        layer.msg("删除成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            window.location.reload()
                        });
                    } else {
                        //失败提示
                        if(resp.msg){
                            layer.msg(resp.msg, {
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
        }, function(index){
            layer.close(index);
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>