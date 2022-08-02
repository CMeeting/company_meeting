<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>管理员管理</h5>
        </div>

        <div class="ibox-content">

            <a href="<?php echo e(route('admins.create')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加管理员</button></a>
























            <form method="post" action="<?php echo e(route('admins.index')); ?>" name="form">

                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th>用户名</th>
                        <th>用户权限</th>
                        <th class="text-center">最后登录IP</th>
                        <th class="text-center" width="150">最后登录时间</th>
                        <th class="text-center" width="150">注册时间</th>
                        <th class="text-center" width="150">注册IP</th>
                        <th class="text-center" width="80">登录次数</th>
                        <th class="text-center" width="80">状态</th>
                        <th class="text-center" width="200">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center"><?php echo e($item->id); ?></td>
                            <td><?php echo e($item->name); ?></td>
                            <td>
                                <?php $__currentLoopData = $item->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo e($role->name); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td class="text-center"><?php echo e($item->last_login_ip); ?></td>
                            <td class="text-center"><?php echo e($item->logintime); ?></td>
                            <td class="text-center"><?php echo e($item->created_at); ?></td>
                            <td class="text-center"><?php echo e($item->create_ip); ?></td>
                            <td class="text-center"><?php echo e($item->login_count); ?></td>
                            <td class="text-center">
                                <?php if($item->status == 1): ?>
                                    <span class="text-navy">正常</span>
                                <?php elseif($item->status == 2): ?>
                                    <span class="text-danger">锁定</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <?php if($item->id ==1): ?>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('admins.edit',$item->id)); ?>">
                                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                                        </a>
                                        <?php if($item->status == 2): ?>
                                                <a href="<?php echo e(route('admins.status',['status'=>1,'id'=>$item->id])); ?>"><button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 恢复</button></a>
                                        <?php else: ?>
                                                <a href="<?php echo e(route('admins.status',['status'=>2,'id'=>$item->id])); ?>"><button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 禁用</button></a>
                                        <?php endif; ?>
                                        <a onclick="del('<?php echo e($item->id); ?>')"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo e($admins->links()); ?>

            </form>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<!-- 显示导出excel模态框（Modal） -->
<div class="modal fade  bs-example-modal-lg" id="ListStyle" aria-hidden="true"
     tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">导出订单</h4>
            </div>



            <div class="panel-body">


                <div class="form-group">
                    <div class="col-xs-12   col-lg-12">
                        <!-- 								<div id="progress_order" class="progress pos-rel" data-percent="0%" > -->
                        <!-- 									<div  id="progress_orderbar"  class="progress-bar" style="width:0%;"></div> -->
                        <!-- 								</div> -->
                        <div id="progress_order" class="progress progress-striped" style="margin-top:1%;">
                            <div id="progress_orderbar" class="progress-bar progress-bar-info active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                <span>0%</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-xs-12   col-lg-12">
                        <button type="button" id="kaishidaochuanniu" class="btn btn-sm purple dropdown-toggle mt-ladda-btn ladda-button " data-style="zoom-in" onclick="return startdaochu();return false;">
                            <i class="fa fa-download"></i>
                            <font >开始导出</font>
                        </button>
                        <a>
                            <button type="button" id="kaishixiazaianniu" class="btn btn-sm purple disabled dropdown-toggle mt-ladda-btn ladda-button  "  onclick="" data-style="zoom-in">
                                <i class="fa fa-download"></i>
                                <font >下载文件</font>
                            </button>
                        </a>
                    </div>
                </div>

            </div>

            <div style="position:relative">
                <div class="panel-body table-responsive" id="searchTable">

                </div>
            </div>

            <div class="modal-footer"></div>

        </div>
    </div>
</div>
<script>
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消']
        }, function(){
            // layer.close(index);
            var index = layer.load();
            $.ajax({
                url: "<?php echo e(route('admins.delete')); ?>",
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
<!--END 显示导出excel模态框（Modal） -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>