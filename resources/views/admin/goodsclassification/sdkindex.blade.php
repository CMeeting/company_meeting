@extends('admin.layouts.layout')
@section('content')
    <style>
        .banben{
            margin-left: 20%;
            color: #529f9d;
        }
        .handless span {

            display: block;
            position: absolute;
            left: 0;
            top: 4px;
            width: 100%;
            text-align: center;
            text-indent: 0;
            color: #fff;
            font-size: 20px;
            font-weight: normal;
        }
        .handless {
            position: absolute;
            margin: 0;
            left: 0;
            top: 0;
            cursor: pointer;
            width: 40px;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 1px solid #aaa;
            background: #ddd;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .abutton{
            display: inline-block;
            border-radius: 10px;
            border: 1px solid lavenderblush;
            margin-right: 3px;
            padding: 0px 5px 0px 5px;
            text-decoration:none;
            color: #f6fff8;
        }
        .cloros{
            background-color: #20e281;
        }
        .cloros1{
            background-color: #e2322d;
        }
        .cloros2{
            background-color: #0b94ea;
        }
        .cloros3{
            background-color: #7f3fe2;
        }
        .cloros4{
            background-color: red;
        }
        #cc{
            display: inline-block;width: 250px;background: #0b94ea;color: aliceblue;border-radius: 15px
        }
        .ab{
            display: inline-block;
            width: 75px;
            height: 35px;
            margin-left: 15px;
            line-height: 35px;
            color: #0b94ea;
            text-decoration: none;
            border: 1px solid royalblue;
            text-align: center;
        }
    </style>

    <div style="width: 100%;margin-bottom: 15px;">
        <a class="ab" href="{{route('goodsclassification.index')}}">SDK</a>
        <a class="ab" style="background: #b4b7b3" href="{{route('goodsclassification.sdkindex')}}">SaaS</a>
    </div>

    <div class="row" id="onType">
        <div class="col-md-12">
            <div class="portlet light ">
                <div class="portlet-title tabbable-line">
                    <div class="actions">
                        <a id="cc" class="addClassify btn btn-md  blue  dropdown-toggle mt-ladda-btn ladda-button" data-style="zoom-in"  type="button" href="{{route('goodsclassification.createsaasgoodsClassification')}}">
                            <i class="fa fa-plus-circle"></i> New SaaSGoodsclassification
                        </a>
                    </div>

                </div>
                <div class="port-body">
                    <div class="dd" id="nestable_list_3">
                        <?php echo $data;?>
                    </div>
                </div>
            </div>

            @endsection
            <script>
                function del(id){
                    layer.confirm('您确定要删除吗？', {
                        btn: ['确定','取消']
                    }, function(){
                        layer.close(index);
                        var index = layer.load();
                        $.ajax({
                            url: "{{route('goodsclassification.delsaasgoodsclassification')}}",
                            data: {delid:id, _token: '{{ csrf_token() }}'},
                            type: 'post',
                            dataType: "json",
                            success: function (resp) {
                                 layer.close(index);
                                //成功提示
                                if (resp.code==0) {
                                    layer.msg("删除成功", {
                                        icon: 1,
                                        time: 1000
                                    }, function () {
                                        location.reload();
                                    });
                                } else {
                                    //失败提示
                                    layer.msg(resp.msg, {
                                        icon: 2,
                                        time: 2000
                                    });
                                }
                            },error:function(response){
                                layer.msg("请检查网络或权限设置！", {
                                    icon: 2,
                                    time: 2000
                                });
                                layer.close(index);
                            }
                        });
                    }, function(index){
                        layer.close(index);
                    });
                }

            </script>