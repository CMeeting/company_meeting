
@extends('admin.layouts.layout')

@section('title', '首页')

@section('css')
  <link href="{{loadEdition('/admin/css/pxgridsicons.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{--  <div class="row state-overview">--}}
{{--    <div class="col-lg-3 col-sm-6">--}}
{{--      <section class="panel">--}}
{{--        <div class="symbol userblue">--}}
{{--          <i class="icon-users"></i>--}}
{{--        </div>--}}
{{--        <div class="value">--}}
{{--          <a href="#"><h1 id="count1">1</h1></a>--}}
{{--          <p>用户总量</p>--}}
{{--        </div>--}}
{{--      </section>--}}
{{--    </div>--}}
{{--    <div class="col-lg-3 col-sm-6">--}}
{{--      <section class="panel">--}}
{{--        <div class="symbol commred">--}}
{{--          <i class="icon-user-add"></i>--}}
{{--        </div>--}}
{{--        <div class="value">--}}
{{--          <a href="#"><h1 id="count2">56</h1></a>--}}
{{--          <p>今日注册用户</p>--}}
{{--        </div>--}}
{{--      </section>--}}
{{--    </div>--}}
{{--    <div class="col-lg-3 col-sm-6">--}}
{{--      <section class="panel">--}}
{{--        <div class="symbol articlegreen">--}}
{{--          <i class="icon-check-circle"></i>--}}
{{--        </div>--}}
{{--        <div class="value">--}}
{{--          <a href="#"><h1 id="count3">1876</h1></a>--}}
{{--          <p>当前Documentation总数</p>--}}
{{--        </div>--}}
{{--      </section>--}}
{{--    </div>--}}
{{--    <div class="col-lg-3 col-sm-6">--}}
{{--      <section class="panel">--}}
{{--        <div class="symbol rsswet">--}}
{{--          <i class="icon-file-word-o"></i>--}}
{{--        </div>--}}
{{--        <div class="value">--}}
{{--          <a href="#"><h1 id="count4">3</h1></a>--}}
{{--          <p>当前blog总数</p>--}}
{{--        </div>--}}
{{--      </section>--}}
{{--    </div>--}}
{{--  </div>--}}
{{--  <div class="row">--}}
{{--    <!-- 表单 -->--}}
{{--    <div class="col-lg-6">--}}
{{--      <section class="panel">--}}
{{--        <header class="panel-heading bm0">--}}
{{--          <span><strong>最新发布Blog</strong></span>--}}
{{--          <span class="tools pull-right">--}}
{{--                                <a class="icon-chevron-down" href="javascript:;"></a>--}}
{{--                            </span>--}}

{{--        </header>--}}
{{--        <div class="panel-body" id="panel-bodys" style="display: block;">--}}
{{--          <table class="table table-hover personal-task">--}}
{{--            <tbody>--}}

{{--            </tbody>--}}
{{--          </table>--}}
{{--        </div>--}}
{{--      </section>--}}
{{--    </div>--}}
{{--    <!-- 表单 -->--}}

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
              <td><strong>程序名称</strong>: ComPDF后台系统 </td>
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
                <strong>操作系统：</strong>：{{PHP_OS}}</td>
              <td></td>
            </tr>
            <tr>
              <td>
                <strong>WEB服务器</strong>：{{php_sapi_name()}}</td>
              <td></td>
            </tr>
            <tr>
              <td>
                <strong>PHP版本</strong>：{{PHP_VERSION}}</td>
              <td></td>
            </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
    <!-- 版权信息 -->
  </div>

@stop
