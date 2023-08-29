@extends('admin.layouts.layout')
@section('content')
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/bootstrap/bootstrap-select.css"/>

    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script src="{{loadEdition('/layui/layui.js')}}"></script>
    <script src="/js/bootstrap/bootstrap.min.js"></script>
    <script src="/js/bootstrap/countrypicker.min.js"></script>
    <script src="/js/bootstrap/bootstrap-select.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <style>
        .custom-file-label {
            background-color: deepskyblue;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 3px;
        }

        .custom-file-label:hover {
            background-color: dodgerblue;
        }
        .alert-warnings {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border-color: #faebcc;
        }

        .alert-dismissables, .alert-dismissible {
            padding-right: 35px;
        }
        .alerts {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <form class="form-horizontal" id="forms" name="form" method="post" action="{{route('fruser.import')}}">
                {{ csrf_field() }}
                <div class="alerts alert-warnings alert-dismissables" style="display: block">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
                    You can choose the method of importing files to enter personnel data.
                    <button class="btn btn-primary btn-sm" type="button" style="background-color: #fcf8e3;border: none;">
                        <label for="file" class="custom-file-label" id="fr_file">Select File</label>
                        <input type="file" id="file" name="file" style="display: none;" onchange="updateFileName(this)"/>
                    </button>
                    <a class="btn btn-primary btn-sm" onclick="submits()">import</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Speakers</h5>
{{--                <div class="btn-group" style="display: inline-block; float: right;">--}}
{{--                    <form class="form-horizontal" id="forms" name="form" method="post"--}}
{{--                          action="{{route('fruser.import')}}">--}}
{{--                        {{ csrf_field() }}--}}
{{--                        <div class="button-container">--}}
{{--                            <a class="btn btn-primary btn-sm" style="background: deepskyblue;" data-style="zoom-in" onclick="submits()">import</a>--}}
{{--                            <button style="float: left" class="btn btn-primary btn-sm" type="button">--}}
{{--                                <label for="file" class="custom-file-label">Select file</label>--}}
{{--                                <input type="file" id="file" name="file" style="display: none;"/>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
            </div>
            <div class="ibox-content">
                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px;padding-left: 0; width: 100%">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('fruser.list')}}">
                        {{ csrf_field() }}
                        <div class="input-group" style="display: flex">
                            <div class="layui-form-item" style="display: flex;">
                                <label class="layui-form-label" style="width:auto;flex-wrap:nowrap">Name</label>
                                <div class="input-group-btn" style="display: inline-block;width: auto;">
                                    <input id="keyword" type="text" name="name" class="form-control"
                                           style="display: inline-block;width: auto;"
                                           value="@if(isset($query['name'])){{$query['name']}}@endif"
                                           placeholder="Speaker's name"/>
                                </div>
                            </div>
                            <div class="layui-form-item" style="display: inline-block;">
                                <label class="layui-form-label" style="width:auto;flex-wrap:nowrap">Status</label>
                                <div class="input-group-btn" style="display: inline-block;width: auto;">
                                    <select id="type" class="form-control"  name="status">
                                        <option value="0">please</option>
                                        @foreach($status_arr as $key=>$type)
                                            <option value={{$key}} @if(isset($query['status'])&&$query['status']==$key) selected @endif>{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item" style="display: inline-block;">
                                <label class="layui-form-label" style="width:auto;flex-wrap:nowrap">Time</label>
                                <div class="input-group-btn" style="display: inline-block;width: auto;">
                                    <input type="text" name="start_date" class="form-control"
                                           style="display: inline-block;width: 160px;" id="startDate"
                                           placeholder="start time"
                                           value="@if(isset($query['start_date'])){{$query['start_date']}}@endif"/>
                                </div>
                                <div class="input-group-btn"
                                     style="display: inline-block;width: auto;margin-left:20px;">
                                    <input type="text" name="end_date" class="form-control"
                                           style="display: inline-block;width: 160px;" id="endDate"
                                           placeholder="end time"
                                           value="@if(isset($query['end_date'])){{$query['end_date']}}@endif"/>
                                </div>
                                <span class="input-group-btn" style="display: inline-block;">
                                <button type="submit" class="btn btn-purple btn-sm">
                                    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                    搜索
                                </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>

                <table class="table table-striped table-bordered table-hover m-t-md"
                       style="word-wrap:break-word; word-break:break-all;">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 0.5%">ID</th>
                        <th class="text-center" style="width: 1.5%">Name</th>
                        <th class="text-center" style="width: 5%">Information(eng)</th>
                        <th class="text-center" style="width: 5%">Information(fr)</th>
                        <th class="text-center" style="width: 1.5%">uuid</th>
                        <th class="text-center" style="width: 1.5%">status</th>
                        <th class="text-center" style="width: 1.5%">Creation time</th>
                        <th class="text-center" style="width: 0.5%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key=>$item)
                        <tr>
                            <td class="text-center">{{$item->id}}</td>
                            <td class="text-center">{{$item->name}}</td>
                            <td class="text-center">{{$item->job_information_eng}}</td>
                            <td class="text-center">{{$item->job_information_fr}}</td>
                            <td class="text-center">{{$item->uuid}}</td>
                            <td class="text-center">{{$status_arr[$item->status]}}</td>
                            <td class="text-center">{{$item->created_at}}</td>
                            <td class="text-center">
                                <div class="btn-group">
{{--                                    <a href="{{route('user.detail', $item->id)}}">--}}
{{--                                        <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i>--}}
{{--                                            查看--}}
{{--                                        </button>--}}
{{--                                    </a>--}}
                                    <a href="{{route('user.edit', $item->id)}}">
                                        <button class="btn btn-warm btn-xs" type="button"><i class="fa fa-paste"></i> 编辑
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <a id="donload"></a>
                    @endforeach
                    </tbody>
                </table>
                {{$data->appends(['keyword' =>isset($query['keyword']) ? $query['keyword'] : '','country'=>isset($query['country']) ? $query['country'] : '','type'=>isset($query['type']) ? $query['type'] : '','start_date'=>isset($query['start_date']) ? $query['start_date'] : '','end_date'=>isset($query['end_date']) ? $query['end_date'] : ''])->links()}}
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script>
        function updateFileName(input){
            var label = document.getElementById("fr_file");
            if (input.files.length > 0) {
                label.innerText = input.files[0].name;
            } else {
                label.innerText = "Select File";
            }
        }

        function submits() {
            var form_data = new FormData($("#forms")[0]);
            var index = layer.load();
            $.ajax({
                url: "{{route('fruser.import')}}",
                processData: false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType: false,
                type: "post",
                data: form_data,
                success: function (data) {
                    location.href = '{{Route("fruser.list")}}'
                }, error: function (ret) {
                    layer.close(index);
                }
            })
        }

        layui.use('laydate', function () {
            let laydate = layui.laydate;

            //执行一个laydate实例
            let start = laydate.render({
                elem: '#startDate', //指定元素
                max: 1,//最大值为当前日期
                trigger: 'click',
                type: 'datetime',//日期时间选择器
                done: function (value, date) {
                    if (value && (value > $("#endDate").val())) {
                        /*开始时间大于结束时间时，清空结束时间*/
                        $("#endDate").val("");
                    }
                    end.config.min = {
                        year: date.year,
                        month: date.month - 1,
                        date: date.date,
                        hours: date.hours,//可注释
                        minutes: date.minutes,//可注释
                        seconds: date.seconds//可注释
                    };
                }
            });
            let end = laydate.render({
                elem: '#endDate', //指定元素
                max: 30,//最大值为当前日期
                type: 'datetime',//日期时间选择器
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });
    </script>
@endsection
