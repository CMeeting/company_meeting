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

        .modal-container {
            display: flex;
            justify-content: left;
            flex-wrap: wrap;
            padding: 10px;
        }

        .modal-label {
            margin-right: 10px;
            width: 200px;
        }

        .modal-title {
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal-buttons button {
            margin-left: 10px;
        }

        .modal-input {
            margin-bottom: 10px;
        }

        #fr_td span {
            white-space: pre-wrap;
            text-align: left;
        }

        #fr_td span::before {
            content: '•';
        }
        #fr_td  span {
            display: block;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Meeting</h5>
                <div class="btn-group" style="display: inline-block; float: right">
                    <a style="" href="{{route('frmeeting.list')}}" link-url="javascript:void(0)">
                        <button class="btn layui-btn-warm btn-sm" type="button">refresh</button>
                    </a>
                    <a style="" href="{{route('frmeeting.create')}}" link-url="javascript:void(0)">
                        <button class="btn layui-btn-danger btn-sm" type="button"><i class="fa fa-paste"></i>Create</button>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="col-xs-10 col-sm-11 margintop5" style="margin-bottom: 5px;padding-left: 0; width: 100%">
                    <form name="admin_list_sea" class="form-search" method="get" action="{{route('frmeeting.list')}}">
                        <div class="input-group" style="display: flex">
                            <div class="layui-form-item" style="display: flex;">
                                <label class="layui-form-label" style="width:auto;flex-wrap:nowrap">Topic</label>
                                <div class="input-group-btn" style="display: inline-block;width: auto;">
                                    <input id="topic" type="text" name="topic" class="form-control"
                                           style="display: inline-block;width: auto;"
                                           value="@if(isset($query['topic'])){{$query['topic']}}@endif"
                                           placeholder="Topic(eng) or Topic(fr)"/>
                                </div>
                            </div>
                            <div class="layui-form-item" style="display: inline-block;">
                                <label class="layui-form-label" style="width:auto;flex-wrap:nowrap">Speaker</label>
                                <div class="input-group-btn" style="display: inline-block;width: auto;">
                                    <select id="speaker" class="form-control"  name="speaker">
                                        <option value="0">please</option>
                                        @foreach($speaker_arr as $key=>$type)
                                            <option value={{$type['id']}} @if(isset($query['speaker'])&&$query['speaker']==$type['id']) selected @endif>{{$type['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item" style="display: inline-block;">
                                <label class="layui-form-label" style="width:auto;flex-wrap:nowrap">Date</label>
                                <div class="input-group-btn" style="display: inline-block;width: auto;">
                                    <input type="text" name="date" class="form-control"
                                           style="display: inline-block;width: 160px;" id="date"
                                           placeholder="Meeting time"
                                           value="@if(isset($query['date'])){{$query['date']}}@endif"/>
                                </div>
                                <span class="input-group-btn" style="display: inline-block;">
                                <button type="submit" class="btn btn-purple btn-sm">
                                    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                    search
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
                        <th class="text-center" style="width: 1.5%">Topic(fr)</th>
                        <th class="text-center" style="width: 1.5%">Topic(fr)</th>
                        <th class="text-center" style="width: 3%">Speaker</th>
                        <th class="text-center" style="width: 1%">Start</th>
                        <th class="text-center" style="width: 1%">End</th>
                        <th class="text-center" style="width: 1%">Creation time</th>
                        <th class="text-center" style="width: 1%">Operation</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key=>$item)
                        <tr>
                            <td class="text-center">{{$item->id}}</td>
                            <td class="text-center">{{$item->topic_fr}}</td>
                            <td class="text-center">{{$item->topic_eng}}</td>
                            <td class="text-center" id="fr_td">
                                @foreach($item['speaker_info'] as $k =>$val)
                                <span>{{$val['name']}} : {{$val['job_information_eng']}}</span>
                                @endforeach
                            </td>
                            <td class="text-center">{{$item->start_time}}</td>
                            <td class="text-center">{{$item->end_time}}</td>
                            <td class="text-center">{{$item->created_at}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{route('fruser.edit', $item->id)}}">
                                        <button class="btn btn-warm btn-xs" type="button"><i class="fa fa-paste"></i> 编辑
                                        </button>
                                        <a onclick="del('{{$item->id}}')"><button class="btn btn-danger del btn-xs" type="button"><i class="fa fa-trash-o"></i> 删除</button></a>
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
        function del(id){
            layer.confirm('Are you sure you want to delete?', {
                btn: ['Confirm','Cancel']
            }, function(){
                var index = layer.load();
                $.ajax({
                    url: "{{route('fruser.softDel')}}",
                    data: {id: id},
                    type: 'get',
                    success: function (resp) {
                        layer.close(index);
                        if (resp.code==0) {
                            layer.msg("Deletion successful", {
                                icon: 1,
                                time: 1000
                            }, function () {
                                window.location.reload()
                            });
                        } else {
                            if(resp.msg){
                                layer.msg(resp.msg, {
                                    icon: 2,
                                    time: 2000
                                });
                            }else {
                                layer.msg("Please check your network or permission settings！！！", {
                                    icon: 2,
                                    time: 2000
                                });
                            }
                        }
                    }
                });
            }, function(index){
                layer.close(index);
            });
        }

        $("#export").click(function () {
            html = '<div class="modal-container">\n' +
                '  <div class="modal-title">\n' +
                '    <label class="modal-label"><input name="id" type="checkbox" value="id" checked="checked"/>id</label>\n' +
                '    <label class="modal-label"><input name="name" type="checkbox" value="name" checked="checked"/>name</label>\n' +
                '    <label class="modal-label"><input name="job_information_eng" type="checkbox" value="job_information_eng" checked="checked"/>Information(eng)</label>\n' +
                '    <label class="modal-label"><input name="job_information_fr" type="checkbox" value="job_information_fr" checked="checked"/>Information(fr)</label>\n' +
                '    <label class="modal-label"><input name="uuid" type="checkbox" value="uuid" checked="checked"/>uuid</label>\n' +
                '    <label class="modal-label"><input name="role" type="checkbox" value="role" checked="checked"/>role</label>\n' +
                '    <label class="modal-label"><input name="status" type="checkbox" value="status" checked="checked"/>status</label>\n' +
                '  </div>\n' +
                '</div>';

            layer.open({
                type: 1,
                title: false,
                closeBtn: 1, //不显示关闭按钮
                shade: [0],
                anim: 2,
                content: html,
                btn:['confirm'],
                area: ['400px', '250px'],
                btn1: function () {
                    let field = [];
                    $("input").each(function (){
                        if($(this).is(':checked')){
                            field.push($(this).val())
                        }
                    })

                    if(field.length == 0){
                        alert("At least one field needs to be selected for exporting.")
                        return false
                    }

                    let name = $('#name').val() ? $('#name').val() : '';
                    let status = $('#status').find("option:selected").val() ? $('#status').find("option:selected").val() : '';
                    let role = $('#role').find("option:selected").val() ? $('#role').find("option:selected").val() : '';
                    let startDate = $('#startDate').val() ? $('#startDate').val() : '';
                    let endDate = $('#endDate').val() ? $('#endDate').val() : '';

                    location.href = "/admin/fruser_list?export=1&name=" + name + "&role=" + role + "&status=" + status + "&start_date=" + startDate + "&end_date=" + endDate
                        + "&field=" + field.join(',');
                }
            });
        });

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
                elem: '#date', //指定元素
                max: 1,//最大值为当前日期
                trigger: 'click',
                type: 'date',//日期时间选择器
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
                type: 'day',//日期时间选择器
                choose: function (datas) {
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            });
        });
    </script>
@endsection
