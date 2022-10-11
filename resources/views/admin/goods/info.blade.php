@extends('admin.layouts.layout')
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Goods Info</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" id="forms" name="form"  method="post" action="{{route('goods.createrungoods')}}" >
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Products：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['level1name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Platform：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['level2name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> License Type：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['level3name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Pricing(USD)：</label>
                    <div class="col-sm-6 col-xs-12" style="padding-top: 0.5%">
                        {{$data['price']}} /year
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
                        <a class="menuid btn btn-primary btn-sm" href="{{route('goods.index')}}">返回</a>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>

@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>

