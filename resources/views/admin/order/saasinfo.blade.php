@extends('admin.layouts.layout')
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5 style="width: 20%;float: left">查看SaaS订单</h5>
                <div class="clearfix form-actions" style="float: right;width: 79.5%">
                    <div class="col-md-offset-5 col-md-9" style="margin-left: 94.5%">
                        <a id="reset" class="menuid btn btn-primary btn-sm" onclick="returnHistory()">返回</a>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <div class="text " style="float: left;width: 79.9%">
                        <p><span style="font-family:'FontAwesome';font-weight:400;font-style:normal;">&nbsp;</span><span style="font-family:'Arial Normal', 'Arial';font-weight:400;"> </span><span style="font-family:'微软雅黑';font-weight:400;">当前订单状态：
                                @if($data[0]['status'] == 1)
                                    <span class="ladda-label">已付款</span>
                                @elseif($data[0]['status'] == 2)
                                    <span class="ladda-label">已完成</span>
                                @elseif($data[0]['status'] == 3)
                                    <span class="ladda-label">待退款</span>
                                @elseif($data[0]['status'] == 4)
                                    <span class="ladda-label">已关闭</span>
                                @else
                                    <span class="ladda-label">待支付</span>
                                @endif
                            </span></p>
                    </div>
                    <div style="float: right;width: 20%;height: 30px">
                        @if($data[0]['status'] == 0)
                        <a style="display: inline-block;width: 80px;height: 30px;text-align: center;padding-top:2px;border: 1px solid cadetblue;color: #0a0a0a" onclick="statusupdate({{$data[0]['order_id']}})">关闭订单</a>
                        @endif
                    </div>
                </div>
                <form class="form-horizontal" id="forms" name="form" method="post" action="">
                    <div class="form-group">
                        <label for="form-field-1" style="margin-left: 15px;"> 基本信息：</label>
                    </div>
                    <table class="table table-striped table-bordered table-hover m-t-md"
                           style="word-wrap:break-word; word-break:break-all;text-align: center">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 12%">子订单编号</th>
                            <th class="text-center" style="width: 15%">套餐类型</th>
                            <th class="text-center" style="width: 9%">档位</th>
                            <th class="text-center" style="width: 9%">有效期(月）</th>
                            <th class="text-center" style="width: 9%">提交时间</th>
                            <th class="text-center" style="width: 8%">用户账号</th>
                            <th class="text-center" style="width: 6%">订单金额</th>
                            <th class="text-center" style="width: 11%">支付方式</th>
                            <th class="text-center" style="width: 6%">订单来源</th>
                            <th class="text-center" style="width: 11%">订单状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $item)
                            <tr id="del_{{$item['id']}}">
                                <td class="text-center">{{$item['goods_no']}}</td>
                                <td class="text-center">{{$item['products']}}</td>
                                <td>{{$item['platform']}}</td>
                                @if($item['pay_years']>0)
                                    <td>{{$item['pay_years']}}/月</td>
                                @else
                                    <td>无期限</td>
                                @endif
                                <td>{{$item['created_at']}}</td>
                                <td>{{$item['email']}}</td>
                                <td>${{$item['price']}}</td>
                                <td>
                                    @if($item['pay_type'] == 1)
                                        <span class="ladda-label">paddle支付</span>
                                    @elseif($item['pay_type'] == 2)
                                        <span class="ladda-label">支付宝</span>
                                    @elseif($item['pay_type'] == 3)
                                        <span class="ladda-label">微信</span>
                                    @elseif($item['pay_type'] == 4)
                                        <span class="ladda-label">不需支付</span>
                                    @else
                                        <span class="ladda-label">未支付</span>
                                    @endif

                                </td>
                                <td>
                                    @if($item['type'] == 1)
                                        <span class="ladda-label">后台创建</span>
                                    @elseif($item['type'] == 2)
                                        <span class="ladda-label">用户购买</span>
                                    @else
                                        <span class="ladda-label">未知</span>
                                    @endif

                                </td>
                                <td>
                                    @if($item['status'] == 1)
                                        <span class="ladda-label">已付款</span>
                                    @elseif($item['status'] == 2)
                                        <span class="ladda-label">已完成</span>
                                    @elseif($item['status'] == 3)
                                        <span class="ladda-label">待退款</span>
                                    @elseif($item['status'] == 4)
                                        <span class="ladda-label">已关闭</span>
                                    @else
                                        <span class="ladda-label">待支付</span>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>



                    <div class="form-group">
                        <label for="form-field-1" style="margin-left: 15px;"> 商品信息：</label>
                    </div>
                    <table class="table table-striped table-bordered table-hover m-t-md"
                           style="word-wrap:break-word; word-break:break-all;text-align: center">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 35%">套餐类型</th>
                            <th class="text-center" style="width: 9%">档位</th>
                            <th class="text-center" style="width: 9%">折扣</th>
                            <th class="text-center" style="width: 9%">合计</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sum=0;?>
                        @foreach($data as $key => $item)
                            <?php $sum+=$item['price'] ;?>
                        <tr id="de">
                            <td class="text-center">{{$item['products']}}</td>
                            <td class="text-center">{{$item['platform']}}</td>
                            <td class="text-center"></td>
                            <td class="text-center">${{$item['price']}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
        </div>
    </div>

@endsection
<script>
    function statusupdate(id) {
        var ale="你确定关闭此订单吗？"
        layer.confirm(ale, {
            btn: ['确定', '取消']
        },function () {
            let index = layer.index;
            $.ajax({
                url: "{{route('order.updatestatus')}}",
                data: {id: id, _token: '{{ csrf_token() }}'},
                type: 'post',
                //dataType: "json",
                success: function (resp) {
                    layer.close(index);
                    layer.close(index);
                    if (resp.code == 0) {
                        layer.msg("关闭成功", {
                            icon: 1,
                            time: 2000
                        });
                        window.location.href='{{route('order.index')}}';
                    } else {
                        //失败提示
                        layer.msg(resp.msg, {
                            icon: 2,
                            time: 2000
                        });
                    }
                }, error: function (response) {
                    layer.msg("请检查网络或权限设置！", {
                        icon: 2,
                        time: 2000
                    });
                    layer.close(index);
                }
            });
        }, function (index) {
            layer.close(index);
        });

    }

    function returnHistory() {
        window.location.href = document.referrer
    }
</script>
