<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Categories</h5>
            <a style="float: right" href="<?php echo e(route('blogs.typeCreate')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Category</button></a>
        </div>
        <div class="ibox-content">

            <div class="col-xs-10 col-sm-5 margintop5" style="margin-bottom: 5px">
                <form name="admin_list_sea" class="form-search" method="get" action="<?php echo e(route('blogs.types')); ?>">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <select name="query_type" class="form-control" style="width: 115px;">
                                <option value="id" <?php if(isset($query)&&$query['query_type']=='id'): ?> selected <?php endif; ?>>ID </option>
                                <option value="title" <?php if(isset($query)&&$query['query_type']=='title'): ?> selected <?php endif; ?>>title </option>
                                <option value="slug" <?php if(isset($query)&&$query['query_type']=='slug'): ?> selected <?php endif; ?>>slug </option>
                                <option value="keywords" <?php if(isset($query)&&$query['query_type']=='keywords'): ?> selected <?php endif; ?>>keywords </option>
                                <option value="seo_title" <?php if(isset($query)&&$query['query_type']=='seo_title'): ?> selected <?php endif; ?>>seo title </option>
                            </select>
                        </div>
                        <input type="text" name="info" class="form-control" value="<?php if(isset($query)): ?><?php echo e($query['info']); ?><?php endif; ?>" />
                        <span class="input-group-btn">
											<button type="submit" class="btn btn-purple btn-sm" style="margin-left: 20px;">
												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
												搜索
											</button>
										</span>
                    </div>
                </form>
            </div>

            <table class="table table-striped table-bordered table-hover m-t-md" style="word-wrap:break-word; word-break:break-all;">
                <thead>
                <tr>
                    <th class="text-center" style="width: 4%">ID</th>
                    <th class="text-center" style="width: 10%">title</th>
                    <th class="text-center" style="width: 10%">slug</th>
                    <th class="text-center" style="width: 10%">seo title</th>
                    <th class="text-center" style="width: 10%">keywords</th>
                    <th class="text-center" style="width: 10%">description</th>
                    <th class="text-center" style="width: 5%">sort_id</th>
                    <th class="text-center" style="width: 8%">created_at</th>
                    <th class="text-center" style="width: 8%">updated_at</th>
                    <th class="text-center" style="width: 10%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td  class="text-center" ><?php echo e($item->id); ?></td>
                        <td><?php echo e($item->title); ?></td>
                        <td class="text-center"><?php echo e($item->slug); ?></td>
                        <td><?php echo e($item->seo_title); ?></td>
                        <td><?php echo e($item->keywords); ?></td>
                        <td><?php echo e($item->description); ?></td>
                        <td><?php echo e($item->sort_id); ?></td>
                        <td><?php echo e($item->created_at); ?></td>
                        <td><?php echo e($item->updated_at); ?></td>

                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?php echo e(route('blogs.typeEdit',$item->id)); ?>"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>

                                <a onclick="del('<?php echo e($item->id); ?>')"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php echo e($data->appends(['info' => isset($query['info'])?$query['info']:'','query_type'=>isset($query['query_type'])?$query['query_type']:''])->links()); ?>

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
                url: "<?php echo e(route('blogs.softDel')); ?>",
                data: {table:'type', id: id},
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