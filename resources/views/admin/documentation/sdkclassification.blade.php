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
            display: inline-block;width: 200px;background: #0b94ea;color: aliceblue;border-radius: 15px
        }
    </style>
    <div class="row" id="onType">
        <div class="col-md-12">
            <div class="portlet light ">
                <div class="portlet-title tabbable-line">
                    <div class="actions">
                        <a id="cc" class="addClassify btn btn-md  blue  dropdown-toggle mt-ladda-btn ladda-button" data-style="zoom-in"  type="button" href="{{route('documentation.createSdkClassification')}}">
                            <i class="fa fa-plus-circle"></i> New SDK Guides
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
                            url: "{{route('documentation.delSdkclassification')}}",
                            data: {delid:id, _token: '{{ csrf_token() }}'},
                            type: 'post',
                            dataType: "json",
                            success: function (resp) {
                                // layer.close(index);
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
                                    layer.msg(resp.message, {
                                        icon: 2,
                                        time: 2000
                                    });
                                }
                            }
                        });
                    }, function(index){
                        layer.close(index);
                    });
                }
                function show(id){
                    var index = layer.load();
                    $.ajax({
                        url: "{{route('documentation.showHideclassification')}}",
                        data: {id:id,type:'sdk_classification', _token: '{{ csrf_token() }}'},
                        type: 'post',
                        dataType: "json",
                        success: function (resp) {
                            if (resp.code==0) {
                                location.reload();
                            } else {
                                //失败提示
                                layer.msg(resp.msg, {
                                    icon: 2,
                                    time: 2000
                                });
                            }
                        }
                    });
                }
            </script>