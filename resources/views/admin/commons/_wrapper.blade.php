<!--右侧部分开始-->
<div id="page-wrapper" class="gray-bg dashbard-1">
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                            class="fa fa-bars"></i> </a>
            </div>

        </nav>
    </div>
    <div class="row content-tabs">
        <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
        </button>
        <nav class="page-tabs J_menuTabs">
            <div class="page-tabs-content">
                <a href="javascript:;" class="active J_menuTab" data-id="index_v1.html">Dashboard</a>
            </div>
        </nav>
        <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
        </button>
        <div class="btn-group roll-nav roll-right">
            <button class="dropdown J_tabClose" data-toggle="dropdown">Close<span class="caret"></span>

            </button>
            <ul role="menu" class="dropdown-menu dropdown-menu-right">
                <li class="J_tabCloseAll"><a>Close all tabs</a>
                </li>
                <li class="J_tabCloseOther"><a>Close other tabs</a>
                </li>
            </ul>
        </div>
        <a href="{{route('admin.logout')}}" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i>
            logout</a>
    </div>
    <div class="row J_mainContent" id="content-main">
        <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="{{route('index.main')}}" frameborder="0" data-id="index_v1.html" seamless></iframe>
    </div>
    <div class="footer">
{{--        <div class="pull-right">&copy; 2015-2017 <a href="http://www.yicms.vip/" target="_blank">{{config('app.name')}}</a>--}}
{{--        </div>--}}
    </div>
</div>
<!--右侧部分结束-->