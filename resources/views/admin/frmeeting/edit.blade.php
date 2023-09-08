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
    <script src="{{loadEdition('/js/jquery.min.js')}}"></script>
    <script src="/js/bootstrap/bootstrap.min.js"></script>
    <script src="/js/bootstrap/countrypicker.min.js"></script>
    <script src="/js/bootstrap/bootstrap-select.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/bootstrap/bootstrap-select.css"/>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox-title">
                <h5>Edit</h5>
            </div>
            <div class="ibox-content">
                <a href="javascript:history.back(-1)"><button class="btn btn-primary btn-sm back" type="button" style="margin-bottom: 40px"><i class="fa fa-chevron-left"></i> 返回列表 </button></a>
                <form class="form-horizontal m-t-md" id="form_data" accept-charset="UTF-8" enctype="multipart/form-data" style="width: 600px;margin: 0 auto;">
                    {!! csrf_field() !!}
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">name：</span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input id="email_input" type="text" placeholder="*Name" class="form-control" name="name" value="{{$row->name}}" required data-msg-required="name required" style="width: 500px"/>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">avatar：</span></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-6">
                            <span class="view picview ">
                                <img id="thumbnail-avatar" class="thumbnail img-responsive" src="{{$row->image}}" width="100" height="100">
                                <button class="btn btn-primary btn-sm" type="button" style="border: none;">
                                    <label for="file" class="custom-file-label" id="fr_file">Select File</label>
                                    <input type="file" id="file" name="file" style="display: none;" onchange="updateFileName(this)"/>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Job Information(eng)：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" placeholder="*Job Information(eng)" class="form-control" name="job_information_eng" value="{{$row->job_information_eng ?? ''}}" required data-msg-required="job information(eng) required" style="width: 500px"/>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">Job Information(fr)：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" placeholder="*Job Information(fr)" class="form-control" name="job_information_fr" value="{{$row->job_information_fr ?? ''}}" required data-msg-required="ob information(fr) required" style="width: 500px"/>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">uuid：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <input type="text" placeholder="*uuid" class="form-control" name="uuid" value="{{$row->uuid ?? ''}}" required data-msg-required="uuid required" style="width: 500px" disabled/>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">role：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <select id="type" class="form-control"  name="role">
                                @foreach($role_arr as $key=>$type)
                                    <option value={{$key}} @if(isset($row->role)&&$row->role==$key) selected @endif>{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="hr-line-dashed m-t-sm m-b-sm" style="position: relative;margin-bottom: 20px;"><span style="font-weight:bold;top: -12px;position: absolute;color:black">status：</span>></div>
                    <div class="form-group" style="padding-left: 18px;">
                        <div class="input-group col-sm-2">
                            <select id="type" class="form-control"  name="status">
                                @foreach($status_arr as $key=>$type)
                                    <option value={{$key}} @if(isset($row->status)&&$row->status==$key) selected @endif>{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <div class="col-sm-12 col-sm-offset-2">
                            <button id="add_user" class="btn btn-primary" type="button" style="margin-right: 30px"><i class="fa fa-check"></i>&nbsp;提交</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
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

        $("#add_user").click(function () {
            let form_data = new FormData($("#form_data")[0]);
            let index = layer.load();

            let name = form_data.get('name').trim()
            if(name == '' || name == null){
                layer.close(index);
                layer.msg('name required', {icon: 2, time: 1000});
                return false;
            }

            let job_information_eng = form_data.get('job_information_eng').trim()
            if(job_information_eng == '' || job_information_eng == null){
                layer.close(index);
                layer.msg('Job Information(eng) required', {icon: 2, time: 1000});
                return false;
            }

            let job_information_fr = form_data.get('job_information_fr').trim()
            if(job_information_fr == '' || job_information_fr == null){
                layer.close(index);
                layer.msg('Job Information(fr) required', {icon: 2, time: 1000});
                return false;
            }

            $.ajax({
                url: "{{route('fruser.update', $row->id)}}",
                data: form_data,
                type: 'post',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                success: function (re) {
                    if (re.code==200) {
                        layer.close(index);
                        layer.open({
                            content:'Would you like to submit the provided information?',
                            btn: ['confirm'],
                            title:'submission successful.',
                            yes: function (index, layero) {
                                location.href = "{{route('fruser.list')}}";
                            }
                        })
                    } else {
                        layer.close(index);
                        //失败提示
                        if(re.msg){
                            layer.open({
                                content:re.msg,
                                btn: ['confirm'],
                                title:'submission failed.'
                            })
                        }else {
                            layer.close(index);
                            layer.msg("Please check your network or permission settings！！！", {
                                icon: 2,
                                time: 2000
                            });
                        }
                    }
                }
            });
        })

        $("#reset_password").click(function () {
            let index = layer.load();
            $.ajax({
                url: "{{route('user.resetPassword', $row->id)}}",
                data: '',
                type: 'get',
                processData:false,//需设置为false。因为data值是FormData对象，不需要对数据做处理
                contentType:false,
                // dataType: "json",
                success: function (re) {
                    //成功提示
                    if (re.code==200) {
                        layer.close(index);
                        layer.open({
                            content:'重置密码成功',
                            btn: ['确认'],
                            title:'提交成功',
                            yes: function (index, layero) {
                                window.history.go(-1);
                            }
                        })
                    } else {
                        layer.close(index);
                        //失败提示
                        if(re.msg){
                            layer.open({
                                content:re.msg,
                                btn: ['确认'],
                                title:'提交失败',
                            })
                        }else {
                            layer.close(index);
                            layer.msg("请检查网络或权限设置！！！", {
                                icon: 2,
                                time: 2000
                            });
                        }
                    }
                }
            });
        })
    </script>
@endsection