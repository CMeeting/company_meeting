@extends('admin.layouts.layout')
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>商品详情</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" id="forms" name="form"  method="post" action="{{route('goods.createrungoods')}}" >
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> 套餐：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['level1name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> 档位（资产数）：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['level2name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> 价格（$）：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['price']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> 排序号：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['sort_num']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> 创建时间：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['created_at']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> 更新时间：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['updated_at']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> 上架时间：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['shelf_at']}}
                    </div>
                </div>


                <div class="clearfix form-actions">
                    <div class="col-md-offset-5 col-md-9">
                        <a class="menuid btn btn-primary btn-sm" href="{{route('goods.saasIndex')}}">返回</a>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>

@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>

