@extends('admin.layouts.layout')
@section('content')
    <div class="row" id="onType">

        <div class="col-md-12">

            <div class="portlet light ">
                <div class="portlet-title tabbable-line">
                    <div class="actions">
                        <button class="addClassify btn btn-md  blue  dropdown-toggle mt-ladda-btn ladda-button" data-style="zoom-in"  type="button"  aria-expanded="false"  onclick="addSpace();">
                            <i class="fa fa-plus-circle"></i> 添加分类
                        </button>
                    </div>

                </div>
                <div class="port-body">
                    <div class="dd" id="nestable_list_3">
                        <div class="layui-fluid" style="min-width: 100%;">
                            <div class="layui-row layui-col-space15">
                                <div class="layui-col-md12"  style="min-height: 500px;overflow-y: auto">
{{--                                    <a class="create"  style="float:none;margin-bottom: 0.6%" href="/admin/documentation/createPlatformVersion">New Platform&Version</a>--}}
                                    <div class="layui-card">
                                        <div class="layui-card-body layui-table-body layui-table-main"
                                             style="min-height: 450px;">
                                            <div class="port-body">

                                                <div class="dd" id="nestable_list_3">
                                                    <ol class="dd-list">
                                                        @if(count($cateList)>0)
                                                        @foreach($cateList as $v)
                                                        <li class="dd-item dd3-item item_{{$v['id']}}" data-id="{{$v['id']}}" id="classSecond_{$v['id']}">
{{--                                                            <div class="dd-handle handless" onclick="zijishow('{{$v['id']}}')"><span id="jiantou_{{$v['id']}}">▷</span></div>--}}
                                                            <div class="dd3-content">
                                                                {{$v['name']}}<span class=" numbid_{{$v['id']}}">&nbsp;&nbsp;<font  style="font-size: 1em">排序</font>:[{{$v['displayorder']}}]</span>

                                                                <div class="item_edt_del">
                                                                    <font class="open_{$v.id}">
                                                                        @if($v['enabled'] == 1)
                                                                        <a  data-id="{$v.id}" style="text-decoration: none"  class="openBtn_{$v.id} abutton cloros" data-style="zoom-out" onclick="show({$v.id});">
                                                                            <span class="ladda-label">show</span>
                                                                        </a>
                                                                        {else}
                                                                        <a data-id="{$v.id}" style="text-decoration: none" class="openBtn_{$v.id} abutton cloros1" data-style="zoom-out" onclick="show({$v.id});">
                                                                            <span class="ladda-label">hide</span>
                                                                        </a>
                                                                        {/if}
                                                                    </font>
                                                                    <a class="abutton cloros2" style="text-decoration: none"  href="/admin/documentation/createPlatformVersion?pid={$v.id}">
                                                                        <i class="fa fa-plus-circle "></i> add
                                                                    </a>
                                                                    <a class="edit_{$v.id} abutton cloros3" style="text-decoration: none"href="/admin/documentation/createPlatformVersion?id={$v.id}">
                                                                        <i class="fa fa-edit"></i> edit
                                                                    </a>

                                                                    <a onclick="del('{$v.id}')" class="abutton cloros4" style="text-decoration: none">
                                                                        <i class="fa fa-trash-o fa-delete"></i> del
                                                                    </a>
                                                                </div>

                                                            </div>
                                                            @if(isset($childCateList[$v['id']]))
                                                            <ol class="dd-list">
                                                                @foreach($childCateList[$v['id']] as $vv)
                                                                <li class="dd-item dd3-item ziji_{$v['id']}" data-id="{$vv['id']}" parentid="{$vv['pid']}" id="classSecond_{$vv['id']}" style="display: none">
                                                                    <div class="dd-handle dd3-handle"></div>
                                                                    <div class="dd3-content">
                                                                        {$vv['name']}<span class=" numbid_{$vv.id}">&nbsp;&nbsp;排序:[{$vv.displayorder}]</span>

                                                                        <div class="item_edt_del">
                                                                            <font class="open_{$vv['id']}">
                                                                                {if $vv['enabled'] == 1}
                                                                                <a  data-id="{$vv.id}"  class="openBtn_{$vv['id']} abutton cloros" data-style="zoom-out" onclick="show({$vv.id});">
                                                                                    <span class="ladda-label">show</span>
                                                                                </a>
                                                                                {else}
                                                                                <a data-id="{$vv.id}"  class="openBtn_{$vv['id']} abutton cloros1" data-style="zoom-out" onclick="show({$vv.id});">
                                                                                    <span class="ladda-label">hide</span>
                                                                                </a>
                                                                                {/if}
                                                                            </font>

                                                                            <a class="edit_{$vv.id} abutton cloros3" href="/admin/documentation/createPlatformVersion?id={$vv.id}">
                                                                                <i class="fa fa-edit"></i> edit
                                                                            </a>

                                                                            <a onclick="del('{$vv.id}')" class="abutton cloros4">
                                                                                <i class="fa fa-trash-o fa-delete"></i> del
                                                                            </a>
                                                                        </div>

                                                                    </div>
                                                                </li>@endforeach
                                                            </ol> @endif
                                                        </li> @endforeach
                                                        @else
                                                        <div style="height: 300px; width: 100%; text-align: center; padding-top: 130px;">
                                                            <div>暂无数据</div>
                                                        </div>
                                                        @endif
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="" style="width: 100%;height: 50px;"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection