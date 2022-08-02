<?php $__env->startSection('content'); ?>
    <script src="<?php echo e(loadEdition('/js/jquery.min.js')); ?>"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Blog</h5>
            <a style="float: right" href="<?php echo e(route('blogs.blogCreate')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Blog</button></a>
        </div>
        <div class="ibox-content">

            <div class="col-xs-10 col-sm-5 margintop5" style="margin-bottom: 5px">
                <form name="admin_list_sea" class="form-search" method="get" action="<?php echo e(route('blogs.blog')); ?>">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <select name="query_type" class="form-control" style="width: 115px;">
                                <option value="id" <?php if(isset($query)&&$query['query_type']=='id'): ?> selected <?php endif; ?>>ID </option>
                                <option value="title_h1" <?php if(isset($query)&&$query['query_type']=='title_h1'): ?> selected <?php endif; ?>>title_h1 </option>
                                <option value="slug" <?php if(isset($query)&&$query['query_type']=='slug'): ?> selected <?php endif; ?>>slug </option>

                                <option value="title" <?php if(isset($query)&&$query['query_type']=='title'): ?> selected <?php endif; ?>>seo title </option>
                            </select>
                        </div>
                        <input type="text" name="info" class="form-control" value="<?php if(isset($query)): ?><?php echo e($query['info']); ?><?php endif; ?>" />
                        <span class="input-group-btn">
											<button type="submit" class="btn btn-purple btn-sm">
												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
												搜索
											</button>
										</span>
                    </div>
                </form>
            </div>










            <table class="table table-striped table-bordered table-hover m-t-md">
                <thead>
                <tr>

                    <th class="text-center">ID</th>
                    <th class="text-center">title_h1</th>
                    <th class="text-center">slug</th>
                    <th class="text-center">categories</th>
                    <th class="text-center">tags</th>
                    <th class="text-center">seo title</th>
                    <th class="text-center">keywords</th>
                    <th class="text-center">sort_id</th>
                    <th class="text-center">created_at</th>
                    <th class="text-center">updated_at</th>
                    <th class="text-center">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td  class="text-center" ><?php echo e($item['id']); ?></td>
                        <td><?php echo e($item['title_h1']); ?></td>
                        <td><?php echo e($item['slug']); ?></td>
                        <td class="text-center"><?php if(isset($types[$item['type_id']])): ?><?php echo e($types[$item['type_id']]); ?><?php endif; ?></td>
                        <td class="text-center"><?php echo e($item->tag_id); ?></td>
                        <td><?php echo e($item['title']); ?></td>
                        <td><?php echo e($item['keywords']); ?></td>
                        <td><?php echo e($item['sort_id']); ?></td>
                        <td><?php echo e($item['created_at']); ?></td>
                        <td><?php echo e($item['updated_at']); ?></td>







                        <td class="text-center">
                            <div class="btn-group">

                                <a href="<?php echo e(route('blogs.blogEdit',$item['id'])); ?>"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>

                                <a onclick="del('<?php echo e($item['id']); ?>')"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>





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
                    data: {table:'blog', id: id},
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
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>