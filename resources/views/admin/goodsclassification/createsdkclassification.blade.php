 @extends('admin.layouts.layout')
@section('content')
    <style>
        dl.layui-anim.layui-anim-upbit {
            z-index: 1000;
        }
        .ccs{
            width: calc(49.5%);
            float: left;
        }


    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>New Goodsclassification</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal" name="form"  method="post" action="{{route('goodsclassification.createRungoodsclassification')}}" >
                    {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> order_num(排序 从小到大)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <input id="displayorder"  type="number" class="form-control" name="data[displayorder]" min="1" max="99999999" oninput="if(value.length>8)value=value.slice(0,8)" value="1" required>
                                        <span class="lbl"></span>
                                    </div>
                                </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1">title(分类名称)</label>
                        <div class="col-sm-6 col-xs-12">
                            <input id="name"  class="form-control" name="data[title]" required>
                            <span class="lbl"></span>
                        </div>
                    </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> classification(上级分类)：</label>
                                    <div class="col-sm-6 col-xs-12">
                                        <select name="data[pid]" class="form-control sels"  id="selectid" @if(isset($pid) && $pid) style="pointer-events: none;color: #9f9f9f" @endif>
                                            <option value="0">--默认一级分类--</option>
                                            @foreach($material as $vs)
                                                <option value="{{$vs['id']}}"
                                                        @if(isset($pid) && $pid==$vs['id'])
                                                        selected
                                                        @endif
                                                >{{$vs['lefthtml']}}{{$vs['title']}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>


                            <div class="clearfix form-actions">
                                <div class="col-md-offset-3 col-md-9">

                                    <button class="btn dropdown-toggle ladda-button" type="submit" id="classifySubmitss"  style="background: deepskyblue" data-style="zoom-in">
                                        保&nbsp;&nbsp;存
                                    </button>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <a class="menuid btn btn-primary btn-sm" href="{{route('goodsclassification.index')}}">返回</a>
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
            $("#name").attr("placeholder","当前添加的是顶级分类名称");
        }else{
            tishi=tishi.substring(1);
            tishi = tishi.replace(/\s+/g,'');
            $("#name").attr("placeholder","当前添加的是"+tishi+"分类下级的分类名称");
        }
        $('#selectid').on('change', function () {
            tishi=$('#selectid').find("option:selected").text();
            tishival=$('#selectid').find("option:selected").val();
            if(tishival==0){
                $("#name").attr("placeholder","当前添加的是顶级分类名称");
            }else{
                tishi=tishi.substring(1);
                tishi = tishi.replace(/\s+/g,'');
                $("#name").attr("placeholder","当前添加的是"+tishi+"分类下级的分类名称");
            }
        })
    })

</script>