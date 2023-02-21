<?php $__env->startSection('title', '首页'); ?>

<?php $__env->startSection('css'); ?>
  <link href="<?php echo e(loadEdition('/admin/css/pxgridsicons.min.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>




































































    <!-- 版权信息 -->
    <div class="col-lg-12">
      <section class="panel">
        <header class="panel-heading bm0">
          <span><strong>团队及版权信息</strong></span>
          <span class="tools pull-right">
                                <a class="icon-chevron-down" href="javascript:;"></a>
                            </span>
        </header>
        <div class="panel-body" id="panel-bodys" style="display: block;">
          <table class="table table-hover personal-task">
            <tbody>
            <tr>
              <td>
                <strong>检测更新</strong>：已是最新版
              </td>
              <td></td>
            </tr>
            <tr>
              <td><strong>程序名称</strong>: ComPDFKit后台系统 </td>
              <td></td>
            </tr>
            <tr>
              <td><strong>当前版本</strong>：V1.0</td>
              <td></td>
            </tr>
            <tr>
              <td><strong>开发团队</strong>：凯钿云端服务组 </td>
              <td></td>
            </tr>
            <tr>
              <td>
                <strong>操作系统：</strong>：<?php echo e(PHP_OS); ?></td>
              <td></td>
            </tr>
            <tr>
              <td>
                <strong>WEB服务器</strong>：<?php echo e(php_sapi_name()); ?></td>
              <td></td>
            </tr>
            <tr>
              <td>
                <strong>PHP版本</strong>：<?php echo e(PHP_VERSION); ?></td>
              <td></td>
            </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
    <!-- 版权信息 -->
  </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>