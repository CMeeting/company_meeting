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
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script src="/tinymce/js/tinymce/tinymce.min.js"></script>
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <script src="{{loadEdition('/layui/layui.js')}}"></script>

    <div class="row">
        <div class="ibox-title" style="margin-bottom:20px">
            <span style="display: inline-block; width: 200px; font-size: 20px">用户详情</span>
            <a href="{{route('user.list')}}"><button class="btn btn-primary btn-sm back" type="button" style="float: right"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
        </div>
        <fieldset class="layui-elem-field">
            <legend style="font-size: 17px">基本信息</legend>
            <div class="layui-field-box">
                <table class="layui-table" style="width: 800px; margin: 0 auto; height: 80px">
                    <tr>
                        <td style="background: #e2e2e2">ID</td>
                        <td>{{$user->id}}</td>
                        <td style="background: #e2e2e2">Email</td>
                        <td>{{$user->email}}</td>
                    </tr>
                    <tr>
                        <td style="background: #e2e2e2">Full Name</td>
                        <td>{{$user->full_name}}</td>
                        <td style="background: #e2e2e2">注册时间</td>
                        <td>{{$user->created_at}}</td>
                    </tr>
                </table>
            </div>
        </fieldset>

        <fieldset class="layui-elem-field">
            <legend style="font-size: 17px">统计信息</legend>
            <div class="layui-field-box">
                <table class="layui-table" style="width: 800px; margin: 0 auto; height: 80px">
                    <caption>SDK</caption>
                    <tr>
                        <td style="background: #e2e2e2">消费金额</td>
                        <td style="background: #e2e2e2">订单数量</td>
                        {{--                        <td style="background: #e2e2e2">优惠券（张）</td>--}}
                        {{--                        <td style="background: #e2e2e2">退款记录</td>--}}
                        <td style="background: #e2e2e2">登录次数</td>
                    </tr>
                    <tr>
                        <td>{{$sdk_info['order_amount']}}</td>
                        <td>{{$sdk_info['order_num']}}</td>
{{--                        <td>###</td>--}}
{{--                        <td>###</td>--}}
                        <td>{{$user->login_times}}</td>
                    </tr>
                </table>
                <br>
                <table class="layui-table" style="width: 800px; margin: 0 auto; height: 80px">
                    <caption>SaaS</caption>
                    <tr>
                        <td style="background: #e2e2e2">消费金额</td>
                        <td style="background: #e2e2e2">总资产</td>
                        <td style="background: #e2e2e2">总资产余量</td>
                        <td style="background: #e2e2e2">资产余量（订阅制）</td>
                        <td style="background: #e2e2e2">资产余量（Package）</td>
                        <td style="background: #e2e2e2">订单数量</td>
                        {{--                        <td style="background: #e2e2e2">优惠券（张）</td>--}}
                        {{--                        <td style="background: #e2e2e2">退款记录</td>--}}
                        <td style="background: #e2e2e2">登录次数</td>
                    </tr>
                    <tr>
                        <td>{{$saas_info['order_amount']}}</td>
                        <td>{{$saas_info['total_assets']}}</td>
                        <td>{{$saas_info['total_assets_balance']}}</td>
                        <td>{{$saas_info['sub_assets_balance']}}</td>
                        <td>{{$saas_info['package_assets_balance']}}</td>
                        <td>{{$saas_info['order_num']}}</td>
                        {{--                        <td>###</td>--}}
                        {{--                        <td>###</td>--}}
                        <td>{{$user->login_times}}</td>
                    </tr>
                </table>
            </div>
        </fieldset>

        <fieldset class="layui-elem-field">
            <legend style="font-size: 17px">Billing Information</legend>
            <div class="layui-field-box">
                <table class="layui-table" style="width: 800px; margin: 0 auto; height: 100px">
                    <tr>
                        <td style="background: #e2e2e2">First Name</td>
                        <td>{{array_get($billing, 'first_name')}}</td>
                        <td style="background: #e2e2e2">Last Name</td>
                        <td>{{array_get($billing, 'last_name')}}</td>
                    </tr>
                    <tr>
                        <td style="background: #e2e2e2">Email</td>
                        <td>{{array_get($billing, 'email')}}</td>
                        <td style="background: #e2e2e2">Phone Number</td>
                        <td>{{array_get($billing, 'phone_number')}}</td>
                    </tr>
                    <tr>
                        <td style="background: #e2e2e2">Country</td>
                        <td>{{array_get($billing, 'country')}}</td>
                        <td style="background: #e2e2e2">State/Province</td>
                        <td>{{array_get($billing, 'province')}}</td>
                    </tr>
                    <tr>
                        <td style="background: #e2e2e2">Company</td>
                        <td>{{array_get($billing, 'company')}}</td>
                        <td style="background: #e2e2e2">City</td>
                        <td>{{array_get($billing, 'city')}}</td>
                    </tr>
                    <tr>
                        <td style="background: #e2e2e2">Address</td>
                        <td>{{array_get($billing, 'address')}}</td>
                        <td style="background: #e2e2e2">Zip/Postal code</td>
                        <td>{{array_get($billing, 'zip')}}</td>
                    </tr>
                </table>
            </div>
        </fieldset>

        <!--
         <fieldset class="layui-elem-field">
            <legend>优惠券详情</legend>
            <div class="layui-field-box">
                <table class="layui-table">
                    <colgroup>
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th style="background: #e2e2e2">优惠券名称</th>
                        <th style="background: #e2e2e2">优惠券类型</th>
                        <th style="background: #e2e2e2">合作方</th>
                        <th style="background: #e2e2e2">优惠码</th>
                        <th style="background: #e2e2e2">使用时间</th>
                        <th style="background: #e2e2e2">优惠券有效期</th>
                        <th style="background: #e2e2e2">订单编号</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>昵称</th>
                        <th>加入时间</th>
                        <th>签名</th>
                        <th>昵称</th>
                        <th>加入时间</th>
                        <th>签名</th>
                        <th>签名</th>
                    </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
         -->


        <fieldset class="layui-elem-field">
            <legend style="font-size: 17px">订单记录</legend>
            <div class="layui-field-box">
                <table class="layui-table">
                    <colgroup>
                        <col width="150">
                        <col width="200">
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th style="background: #e2e2e2">订单编号</th>
                        <th style="background: #e2e2e2">用户账号</th>
                        <th style="background: #e2e2e2">支付方式</th>
                        <th style="background: #e2e2e2">订单金额</th>
                        <th style="background: #e2e2e2">订单状态</th>
                        <th style="background: #e2e2e2">订单来源</th>
                        <th style="background: #e2e2e2">订单类型</th>
                        <th style="background: #e2e2e2">创建时间</th>
                        <th style="background: #e2e2e2">支付时间</th>
                        <th style="background: #e2e2e2">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <th>{{$order->order_no}}</th>
                            <th>{{$user->email}}</th>
                            <th>{{array_get($pay_type_arr, $order->pay_type)}}</th>
                            <th>{{$order->price}}</th>
                            <th>{{array_get($status_arr, $order->status)}}</th>
                            <th>{{array_get($source_arr, $order->type)}}</th>
                            <th>{{array_get($details_type_arr, $order->details_type)}}</th>
                            <th>{{$order->created_at}}</th>
                            <th>{{$order->pay_time}}</th>

                            <th>
                                <div class="btn-group">
                                    @if($order->details_type == 3)
                                        <a  href="{{route('order.getsaasinfo',$order->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 查看订单</button></a>
                                    @else
                                        <a  href="{{route('order.getinfo',$order->id)}}"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 查看订单</button></a>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$orders->links()}}
            </div>
        </fieldset>

    </div>
@endsection