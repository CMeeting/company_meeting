@extends('admin.layouts.layout')
@section('content')
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>修改头像</h5>
        </div>
        <div class="ibox-content">
{{--            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>--}}
{{--            <a href="{{route('admins.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>--}}
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ route('admin.updateAvatar',$admin->id) }}" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">原头像：</label>
                        <div class="input-group col-sm-6">
                            <span class="view picview ">
                                <img id="thumbnail-avatar" class="thumbnail img-responsive" src="{{$admin->avatr}}" width="100" height="100">
                            </span>
                        </div>
                    </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">新头像：</label>
                    <div class="input-group col-sm-6">
                        <input type="file" class="form-control" name="avatr" style="width: auto;">
                    </div>
                </div>

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