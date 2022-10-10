@extends('admin.layouts.layout')
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Goods Info</h5>
            </div>
            <div class="ibox-content">

                <div class="form-group" style="width: 100%">
                    <label style="float: left" class="col-sm-4 control-label no-padding-right" for="form-field-1"> Products：</label>
                    <div  style="float: left" class="col-sm-6 col-xs-12">
                        {{$data['level1name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label  style="float: left" class="col-sm-2 control-label no-padding-right" for="form-field-1"> Platform：</label>
                    <div  style="float: left" class="col-sm-6 col-xs-12">
                        {{$data['level2name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label  style="float: left" class="col-sm-2 control-label no-padding-right" for="form-field-1"> License Type：</label>
                    <div  style="float: left" class="col-sm-6 col-xs-12">
                        {{$data['level3name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Pricing(USD)：</label>
                    <div class="col-sm-6 col-xs-12">
                        {{$data['price']}} /year
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 创建时间：</label>
                    <div class="col-sm-6 col-xs-12">
                        {{$data['created_at']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 更新时间：</label>
                    <div class="col-sm-6 col-xs-12">
                        {{$data['updated_at']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 上架时间：</label>
                    <div class="col-sm-6 col-xs-12">
                        {{$data['shelf_at']}}
                    </div>
                </div>


                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <a class="menuid btn btn-primary btn-sm" href="{{route('goods.index')}}">返回</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>

