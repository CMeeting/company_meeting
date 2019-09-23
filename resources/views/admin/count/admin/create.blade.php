@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>
            @if($item) 修改 @else 添加 @endif
            {{$showKeyArr[$level]}}
            </h5>
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="{{route('config.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> {{$showKeyArr[$level]}}</button></a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <form class="form-horizontal m-t-md" action="{{ route('config.opeary',$item?$item['id']:0) }}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                {!! csrf_field() !!}
                {{method_field('POST')}}
                
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{$showKeyArr[$level]}}名称：</label>
                    <div class="input-group col-sm-2">
                        <input type="value" class="form-control" name="value" value="{{$item?$item['value']:""}}">
                        @if ($errors->has('value'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('value')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">手机号码：</label>
                    <div class="input-group col-sm-2">
                        <input type="value" class="form-control" name="value" value="{{$item?$item['value']:""}}">
                        @if ($errors->has('value'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('value')}}</span>
                        @endif
                    </div>
                </div>
                
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">登录账号：</label>
                    <div class="input-group col-sm-2">
                        <input type="value" class="form-control" name="value" value="{{$item?$item['value']:""}}">
                        @if ($errors->has('value'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('value')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">登录密码：</label>
                    <div class="input-group col-sm-2">
                        <input type="value" class="form-control" name="value" value="{{$item?$item['value']:""}}">
                        @if ($errors->has('value'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('value')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">代理人真实姓名：</label>
                    <div class="input-group col-sm-2">
                        <input type="value" class="form-control" name="value" value="{{$item?$item['value']:""}}">
                        @if ($errors->has('value'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('value')}}</span>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">代理人身份证：</label>
                    <div class="input-group col-sm-2">
                        <input type="value" class="form-control" name="value" value="{{$item?$item['value']:""}}">
                        @if ($errors->has('value'))
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i>{{$errors->first('value')}}</span>
                        @endif
                    </div>
                </div>
                
                <input type="hidden" class="form-control"   name="id" value="{{$item?$item['id']:""}}">
                <input type="hidden" class="form-control"  name="key" value="{{$level}}">
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <div class="col-sm-12 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                        <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
</div>
@endsection