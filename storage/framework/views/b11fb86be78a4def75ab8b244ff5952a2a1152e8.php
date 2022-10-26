<?php $__env->startSection('content'); ?>
    <script src="<?php echo e(loadEdition('/js/jquery.min.js')); ?>"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="<?php echo e(loadEdition('/layui/layui.js')); ?>"></script>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Blog</h5>
                <a style="float: right" href="<?php echo e(route('blogs.blogCreate')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加 Blog</button></a>
            </div>
            <div class="ibox-content">

                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px">
                    <form name="admin_list_sea" class="form-search" method="get" action="<?php echo e(route('blogs.blog')); ?>" style="width: 100%;overflow: auto;">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <select name="query_type" class="form-control" style="display: inline-block;width: 100px;">
                                    <option value="id" <?php if(isset($query)&&$query['query_type']=='id'): ?> selected <?php endif; ?>>ID </option>
                                    <option value="title_h1" <?php if(isset($query)&&$query['query_type']=='title_h1'): ?> selected <?php endif; ?>>title_h1 </option>
                                    <option value="slug" <?php if(isset($query)&&$query['query_type']=='slug'): ?> selected <?php endif; ?>>slug </option>
                                    
                                    <option value="title" <?php if(isset($query)&&$query['query_type']=='title'): ?> selected <?php endif; ?>>seo title </option>
                                </select>
                            </div>
                            <input type="text" name="info" class="form-control" style="display: inline-block;width: 150px;" value="<?php if(isset($query)): ?><?php echo e($query['info']); ?><?php endif; ?>" />
                            <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                                <select class="form-control"  name="type_id">
                                    <option value="">筛选Category</option>
                                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($k); ?>" <?php if(isset($query)&&$query['type_id']==$k): ?> selected <?php endif; ?>><?php echo e($v); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                                <input type="text"  name="start_date" class="form-control" style="display: inline-block;width: 160px;" id="startDate" placeholder="创建时间--开始" value="<?php if(isset($query)): ?><?php echo e($query['start_date']); ?><?php endif; ?>" />
                            </div>
                            <div class="input-group-btn" style="display: inline-block;width: 150px;margin-left:20px;">
                                <input type="text"  name="end_date" class="form-control" style="display: inline-block;width: 160px;" id="endDate" placeholder="创建时间--结束" value="<?php if(isset($query)): ?><?php echo e($query['end_date']); ?><?php endif; ?>" />
                            </div>
                            <span class="input-group-btn" style="display: inline-block;">
											<button type="submit" class="btn btn-purple btn-sm" style="margin-left: 20px;">
												<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
												搜索
											</button>
										</span>
                        </div>

                    </form>
                </div>


                
                

                
                
                
                
                
                <form name="form" style="width: 100%;overflow: auto;margin-top: 10px;">
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
                <?php echo e($data->appends(['info' => isset($query['info'])?$query['info']:'','query_type'=>isset($query['query_type'])?$query['query_type']:'','type_id'=>isset($query['type_id'])?$query['type_id']:'','start_date'=>isset($query['start_date'])?$query['start_date']:'','end_date'=>isset($query['end_date'])?$query['end_date']:''])->links()); ?>

                </form>
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

        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //执行一个laydate实例
            var start = laydate.render({
                elem: '#startDate', //指定元素
                max:1,//最大值为当前日期
                trigger: 'click',
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-30),//默认值30天前
                done:function(value,date){
                    if(value && (value>$("#endDate").val())){
                        /*开始时间大于结束时间时，清空结束时间*/
                        $("#endDate").val("");
                    }
                    end.config.min ={
                        year:date.year,
                        month:date.month-1,
                        date: date.date,
                        hours:date.hours,//可注释
                        minutes:date.minutes,//可注释
                        seconds:date.seconds//可注释
                    };
                }
            });
            var end = laydate.render({
                elem: '#endDate', //指定元素
                max : 1,//最大值为当前日期
                type: 'datetime',//日期时间选择器
                // value: getRecentDay(-1),//默认值昨天
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });

        // /**获取近N天*/
        // function getRecentDay(day){
        //     var today = new Date();
        //     var targetday_milliseconds=today.getTime() + 1000*60*60*24*day;
        //     today.setTime(targetday_milliseconds);
        //     var tYear = today.getFullYear();
        //     var tMonth = today.getMonth();
        //     var tDate = today.getDate();
        //     var tHours = today.getHours();//可注释
        //     var tMinutes = today.getMinutes();//可注释
        //     var tSeconds = today.getSeconds();//可注释
        //     tMonth = doHandleMonth(tMonth + 1);
        //     tDate = doHandleMonth(tDate);
        //     return tYear+"-"+tMonth+"-"+tDate+" "+tHours+":"+tMinutes+":"+tSeconds;
        // }
        // /**获取近N月*/
        // function doHandleMonth(month){
        //     var m = month;
        //     if(month.toString().length == 1){
        //         m = "0" + month;
        //     }
        //     return m;
        // }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>