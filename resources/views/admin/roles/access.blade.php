@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>{{$role->name}} - 授权</h5>
        </div>
        <div class="ibox-content">
            <a href="{{route('roles.index')}}"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
{{--            <a href="{{route('roles.create')}}" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加角色</button></a>--}}

            <form class="form-horizontal m-t-md" action="{{route('roles.group-access',$role->id)}}" method="post">
                {{csrf_field()}}
                <div class="form-group">
                    <table class="table table-striped table-bordered table-hover table-condensed">
                        @foreach($datas as $k=>$item)
                            @if(empty($item->_data))
                                @if(1!=$item->id)
                                <tr class="b-group">
                                    <th width="10%">
                                        <label>
                                            &nbsp;&nbsp;{{$item->name}}&nbsp;
                                            <input type="checkbox" name="rule_id[]" value="{{$item->id}}" onclick="checkAll(this)" @if(in_array($item->id,$rules)) checked="checked" @endif id="dj_{{$item->id}}" class="dj_{{$item->id}}">
                                        </label>
                                    </th>
                                    <td></td>
                                </tr>
                                @endif
                            @else
                                @if(1!=$item->id)
                                <tr class="b-group">
                                    <th width="10%">
                                        <label>
                                            &nbsp;&nbsp;{{$item->name}}&nbsp;<input type="checkbox" name="rule_id[]" value="{{$item->id}}" @if(in_array($item->id,$rules)) checked="checked" @endif onclick="checkAll(this)"id="dj_{{$item->id}}" class="dj_{{$item->id}}">
                                        </label>
                                    </th>
                                    <td class="b-child">
                                        @foreach($item->_data as $key=>$value)
                                            <table class="table table-striped table-bordered table-hover table-condensed">
                                                <tr class="b-group">
                                                    <th width="10%">
                                                        <label>
                                                            {{$value->name}}&nbsp;<input type="checkbox" name="rule_id[]" value="{{$value->id}}" @if(in_array($value->id,$rules)) checked="checked" @endif onclick="checkAll(this);checkzji(0,'{{$value->id}}','{{$item->id}}')"id="fj_{{$value->id}}" class="dj_{{$item->id}} fj_{{$value->id}}">
                                                        </label>
                                                    </th>
                                                    <td>
                                                        @if(!empty($value->_data))
                                                            @foreach($value->_data as $val)
                                                                <label>
                                                                    &emsp;{{$val->name}} <input type="checkbox" name="rule_id[]" value="{{$val->id}}" @if(in_array($val->id,$rules)) checked="checked"  @endif id="zj_{{$val->id}}" class="fj_{{$value->id}} zj_{{$val->id}}" onclick="checkzji('{{$val->id}}','{{$value->id}}','{{$item->id}}')">
                                                                </label>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif
                            @endif
                        @endforeach
                        <tr>
                            <th></th>
                            <td>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保存</button>
                                <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function checkAll(obj){
        $(obj).parents('.b-group').eq(0).find("input[type='checkbox']").prop('checked', $(obj).prop('checked'));
    }
    function checkzji(zj,fj,dj){
        if(zj==0){
            if($("#fj_"+fj).is(':checked')){
               $("#dj_"+dj).prop("checked", true);
            }else{
                var i=0;
                $(".dj_"+dj).each(function (){
                    if($(this).is(':checked')){
                        i++;
                    }
                })
                if(i==1){
                    $("#dj_"+dj).attr("checked", false);
                }
            }
        }else{
            if($("#zj_"+zj).is(':checked')){
                    $("#fj_"+fj).prop("checked", true);
                    $("#dj_"+dj).prop("checked", true);
            }else{
                var s=0;
                $(".fj_"+fj).each(function (){
                    if($(this).is(':checked')){
                        s++;
                    }
                })
                if(s==1){
                    $("#fj_"+fj).attr("checked", false);

                    var i=0;
                    $(".dj_"+dj).each(function (){
                        if($(this).is(':checked')){
                            i++;
                        }
                    })
                    if(i==1){
                        $("#dj_"+dj).attr("checked", false);
                    }
                }
            }
        }
    }
</script>
@endsection