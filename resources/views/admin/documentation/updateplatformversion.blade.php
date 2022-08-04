@extends('admin.layouts.layout')
@section('content')
    <style>
        dl.layui-anim.layui-anim-upbit {
            z-index: 1000;
        }
        .sels{
            display: inline-block;
            width: calc(88.5% - 22px);
            border: 1px solid #c9d0d6;
            border-radius: 3px;
            font-size: 0.95em;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            outline: none;
            padding: 8px 10px 7px;
        }
        .sels{
            display: inline-block;
            width: calc(88.5% - 22px);
            border: 1px solid #c9d0d6;
            border-radius: 3px;
            font-size: 0.95em;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            outline: none;
            padding: 8px 10px 7px;
        }

    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Edit Platform&Product</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" name="form"  method="post" action="{{route('documentation.updateRunPlatformVersion')}}" >
                    {{ csrf_field() }}
                                <input type="hidden" name="data[id]" value="{{$data['id']}}">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> order_num(排序 从小到大)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="displayorder"  type="number" class="form-control" name="data[displayorder]" min="1" max="99999999" oninput="if(value.length>8)value=value.slice(0,8)" value="{{$data['displayorder']}}" required>
                                        <span class="lbl"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> classification(上级分类)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <select name="data[pid]" class="form-control sels"  id="selectid"  style="pointer-events: none;color: #9f9f9f">
                                            <option value="0">--默认一级分类--</option>
                                            @foreach($material as $vs)
                                                <option value="{{$vs['id']}}"
                                                        @if(isset($data['pid']) && $data['pid']==$vs['id'])
                                                        selected
                                                        @endif
                                                >{{$vs['lefthtml']}}{{$vs['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> name(平台或版本名称)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="name"  class="form-control" name="data[name]" value="{{$data['name']}}" required maxlength="25">
                                        <span class="lbl"></span>
                                    </div>
                                </div>


                                @if(isset($data['pid']) && $data['pid'] == 0)

                                    <div class="form-group h1title">
                                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> SEO title：</label>
                                        <div class="col-sm-6 col-xs-12">
                                            <input id="seotitel"  class="form-control  seotitel" name="data[seotitel]" required placeholder="SEO title只会绑定在平台数据" value="{{$data['seotitel']}}">
                                            <span class="lbl"></span>
                                        </div>
                                    </div>
                                    <div class="form-group h1title">
                                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> H1 title：</label>
                                        <div class="col-sm-6 col-xs-12">
                                            <input id="h1title"  class="form-control " name="data[h1title]" required placeholder="H1 title只会绑定在平台数据" value="{{$data['h1title']}}">
                                            <span class="lbl"></span>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> enabled(是否显示)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="radio" name="data[enabled]" value="1" @if(isset($data['enabled']) && $data['enabled']==1) checked @endif >显示
                                        <input type="radio" name="data[enabled]" value="0" @if(isset($data['enabled']) && $data['enabled']!=1) checked @endif>隐藏
                                    </div>
                                </div>
                            </ol>

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
    <script type="text/javascript">
        $(function (){
            var tishi=$('#selectid').find("option:selected").text();
            var tishival=$('#selectid').find("option:selected").val();
            if(tishival==0){
                $("#name").attr("placeholder","当前添加的是平台名称");
            }else{
                tishi=tishi.substring(1);
                $("#name").attr("placeholder","当前添加的是"+tishi+"下的产品名称");
            }
            $('#selectid').on('change', function () {
                tishi=$('#selectid').find("option:selected").text();
                tishival=$('#selectid').find("option:selected").val();
                if(tishival==0){
                    $(".seotitel").show();
                    $(".h1title").show();
                    $("#seotitel").prop('required',true);
                    $("#h1title").prop('required',true);
                    $("#name").attr("placeholder","当前添加的是平台名称");
                }else{
                    $(".seotitel").hide();
                    $(".h1title").hide();
                    $("#seotitel").prop('required',false);
                    $("#h1title").prop('required',false);
                    tishi=tishi.substring(1);
                    $("#name").attr("placeholder","当前添加的是"+tishi+"下的产品名称");
                }
            })
        })
    </script>