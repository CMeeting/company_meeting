@extends('admin.layouts.layout')
@section('content')
    <style>
        .ccs{
            width: calc(100%);
        }

    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Edit Sdk Documentation</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" name="form"  method="post" action="{{route('documentation.updateRunsdkDocumentation')}}" >
                    {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> order_num(排序 从大到小)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="displayorder"  type="number" class="form-control" name="data[displayorder]" min="1" max="99999999" oninput="if(value.length>8)value=value.slice(0,8)" value="{{$data['displayorder']}}" required>
                                        <span class="lbl"></span>
                                    </div>
                                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Title H1(文章名称)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="name"  class="form-control" name="data[titel]" value="{{$data['titel']}}" required>
                            <span class="lbl"></span>
                        </div>
                    </div>

                    <div class="form-group h1title">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> SEO Title：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="seotitel"  class="form-control  seotitel" name="data[seotitel]" value="{{$data['seotitel']}}" required>
                            <span class="lbl"></span>
                        </div>
                    </div>
                    <div class="form-group h1title">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Slug(确保唯一性)：</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="Slug"  class="form-control " name="data[slug]" value="{{$data['slug']}}" required onKeyUp="value=value.replace(/[^\w\.\/-]/ig,'')">
                            <span class="lbl"></span>
                        </div>
                    </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> classification(文章分类)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <select name="data[classification_ids]" class="form-control ccs"  id="classification_ids" @if(isset($data['classification_ids']) && $data['classification_ids']) style="pointer-events: none;color: #9f9f9f" @endif>
                                            <option value="0">--请选择文章分类--</option>
                                            @foreach($material as $vs)
                                                <option value="{{$vs['id']}}"
                                                        @if(isset($data['classification_ids']) && $data['classification_ids']==$vs['id'])
                                                        selected
                                                        @endif
                                                >{{$vs['lefthtml']}}{{$vs['title']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> content：</label>
                        <div class="col-sm-6 col-xs-12">
                            <textarea id="content" name="data[info]">{{$data['info']}}</textarea>
                        </div>
                    </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> enabled(是否显示)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="radio" name="data[enabled]" value="1" @if($data['enabled']==1) checked @endif>显示
                                        <input type="radio" name="data[enabled]" value="0" @if($data['enabled']!=1) checked @endif>隐藏
                                    </div>
                                </div>


                            <div class="clearfix form-actions">
                                <div class="col-md-offset-3 col-md-9">

                                    <button class="btn dropdown-toggle ladda-button" type="submit" id="classifySubmitss"  style="background: deepskyblue" data-style="zoom-in">
                                        保&nbsp;&nbsp;存
                                    </button>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
                                </div>
                            </div>
                    </form>
            </div>
        </div>
    </div>

@endsection
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
