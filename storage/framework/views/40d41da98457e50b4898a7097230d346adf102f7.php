<?php $__env->startSection('content'); ?>
    <style>
        .abutton{
            display: inline-block;
            border-radius: 10px;
            border: 1px solid lavenderblush;
            margin-right: 3px;
            width: 75px;
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
    </style>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>Documentation</h5>
            <a style="float: right" href="<?php echo e(route('documentation.createsdkDocumentation')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> New Sdkdocumentation</button></a>
        </div>

        <div class="ibox-content">
            <form name="admin_list_sea" class="form-search" method="get" action="<?php echo e(route('documentation.sdkDocumentation')); ?>">
            <div class="col-xs-10 col-sm-5 margintop5">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <select name="query_type" class="form-control" style="width: 115px;">
                                <option value="id" <?php if(isset($query)&&$query['query_type']=='id'): ?> selected <?php endif; ?>>ID </option>
                                <option value="titel" <?php if(isset($query)&&$query['query_type']=='title_h1'): ?> selected <?php endif; ?>>Title H1 </option>
                                <option value="seotitel" <?php if(isset($query)&&$query['query_type']=='seotitel'): ?> selected <?php endif; ?>>SEO Title </option>
                                <option value="slug" <?php if(isset($query)&&$query['query_type']=='slug'): ?> selected <?php endif; ?>>Slug </option>
                            </select>
                        </div>
                        <input type="text" name="info" class="form-control" value="<?php if(isset($query)): ?><?php echo e($query['info']); ?><?php endif; ?>" />
                    </div>
            </div>
            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                <div class="form-group">
                    <select class="form-control"  name="platformid" tabindex="1">
                        <option value="">筛选平台</option>
                        <?php $__currentLoopData = $platformid; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if(isset($query)&&$query['platformid']==$k): ?> selected <?php endif; ?>><?php echo e($v); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                <div class="form-group">
                    <select class="form-control"  name="version" tabindex="1">
                        <option value="">筛选版本</option>
                        <?php $__currentLoopData = $version; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if(isset($query)&&$query['version']==$k): ?> selected <?php endif; ?>><?php echo e($v); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
                <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <select class="form-control"  name="version" tabindex="1">
                            <option value="">筛选分类</option>
                            <?php $__currentLoopData = $classification_ids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k); ?>" <?php if(isset($query)&&$query['version']==$k): ?> selected <?php endif; ?>><?php echo e($v); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <span class="input-group-btn">
											<button type="submit" class="btn btn-purple btn-sm">
												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
												搜索
											</button>
										</span>
            </form>


            <form method="post" action="<?php echo e(route('documentation.sdkDocumentation')); ?>" name="form">

                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th>Title H1</th>
                        <th>SEO Title</th>
                        <th class="text-center">Slug</th>
                        <th class="text-center" width="150">platformversion</th>
                        <th class="text-center" width="150">classification</th>
                        <th class="text-center" width="150">created_at</th>
                        <th class="text-center" width="80">updated_at</th>
                        <th class="text-center" width="200">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr id="han_<?php echo e($value->id); ?>">
                            <td class="text-center"><?php echo e($value->id); ?></td>
                            <td><?php echo e($value->titel); ?></td>
                            <td class="text-center"><?php echo e($value->seotitel); ?></td>
                            <td class="text-center"><?php echo e($value->slug); ?></td>
                            <td class="text-center"><?php echo e($value->platformversion); ?></td>
                            <td class="text-center"><?php echo e($value->classification); ?></td>
                            <td class="text-center"><?php echo e($value->created_at); ?></td>
                            <td class="text-center"><?php echo e($value->updated_at); ?></td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <font class="open_<?php echo e($value->id); ?>">
                                        <?php if($value->enabled == 1): ?>
                                        <a type="button" style="text-decoration: none;color: #f6fff8"   data-id="{$v.id}"  class="openBtn_<?php echo e($value->id); ?> abutton cloros" data-style="zoom-out" onclick="show(<?php echo e($value->id); ?>);">
                                            <span class="ladda-label">show</span>
                                        </a>
                                        <?php else: ?>
                                        <a type="button" style="text-decoration: none;color: #f6fff8"  data-id="{$v.id}"  class="openBtn_<?php echo e($value->id); ?> abutton cloros1" data-style="zoom-out" onclick="show(<?php echo e($value->id); ?>);">
                                            <span class="ladda-label">hide</span>
                                        </a>
                                        <?php endif; ?>
                                    </font>
                                    <a class="edit_3 abutton cloros3" style="text-decoration: none;color: #f6fff8" title="Edit " href="<?php echo e(route('documentation.updatesdkDocumentation',$value->id)); ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a class="abutton cloros4" style="text-decoration: none;color: #f6fff8" title="Delete" onclick="del('<?php echo e($value->id); ?>')">
                                        <i class="fa fa-trash-o fa-delete"></i>Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo e($data->appends(['query' => isset($query)?$query:'','query_type'=>isset($query['query_type'])?$query['query_type']:'','platformid'=>isset($query['platformid'])?$query['platformid']:'','version'=>isset($query['version'])?$query['version']:'','classification_ids'=>isset($query['classification_ids'])?$query['classification_ids']:''])->links()); ?>

            </form>
        </div>
    </div>
    <div class="clearfix"></div>
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
                url: "<?php echo e(route('documentation.delsdkDocumentation')); ?>",
                data: {delid:id, _token: '<?php echo e(csrf_token()); ?>'},
                type: 'post',
                dataType: "json",
                success: function (resp) {
                     layer.close(index);
                    //成功提示
                    if (resp.code==0) {
                        layer.msg("删除成功", {
                            icon: 1,
                            time: 1000
                        }, function () {
                            $("#han_"+id).remove();
                        });
                    } else {
                        //失败提示
                        layer.msg(resp.message, {
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
            url: "<?php echo e(route('documentation.showHideclassification')); ?>",
            data: {id:id,type:'sdk_documentation', _token: '<?php echo e(csrf_token()); ?>'},
            type: 'post',
            dataType: "json",
            success: function (resp) {
                if (resp.code==0) {
                    if(resp.status==1){
                        var htmls='<a type="button" style="text-decoration: none;color: #f6fff8"   data-id="{$v.id}"  class="openBtn_'+id+' abutton cloros" data-style="zoom-out" onclick="show('+id+');"> <span class="ladda-label">show</span></a>';
                    }else{
                        var htmls='<a type="button" style="text-decoration: none;color: #f6fff8"  data-id="{$v.id}"  class="openBtn_'+id+' abutton cloros1" data-style="zoom-out" onclick="show('+id+');"> <span class="ladda-label">hide</span></a>';
                    }
                    $(".open_"+id).html(htmls);
                    layer.close(index);
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
</script>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>