@extends('admin.layouts.layout')
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>授权码详情</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" id="forms" name="form">
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">总订单号：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['order_id']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">子订单号：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['order_no']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">商品名称：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">APP ID/Machine ID：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['uuid']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">授权码：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['license_key']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">授权码：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['license_key']}}"
                    </div>
                </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1">license_secret：</label>
                        <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                            {{$info['license_secret']}}"
                        </div>
                    </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">类型：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['type']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">状态：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['status']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">生成时间：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['created_at']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1">过期时间：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$info['expire_time']}}
                    </div>
                </div>
                <div class="clearfix form-actions">
                    <div class="col-md-offset-5 col-md-9">
                        <a class="menuid btn btn-primary btn-sm" href="{{route('license.index')}}">返回</a>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>

@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>

