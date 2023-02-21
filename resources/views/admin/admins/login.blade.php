<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>后台登录 - {{ config('app.name', 'Laravel') }}</title>
    <meta name="keywords" content="后台登录">
    <meta name="description" content="后台登录">
    <link href="{{loadEdition('/admin/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/style.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/login.min.css')}}" rel="stylesheet">
    <script>
        if(window.top!==window.self){window.top.location=window.location};
    </script>
{{--    <script src="https://www.google.com/recaptcha/api.js" async defer></script>--}}
{{--    <script>--}}
{{--        var RecaptchaOptions = {--}}
{{--            theme : 'custom',--}}
{{--            custom_theme_widget: 'recaptcha_widget'--}}
{{--        };--}}
{{--    </script>--}}
</head>

<body class="signin">
    <div class="signinpanel">
        <div class="row">
            <div class="col-sm-5 animated fadeInLeft">
                <div class="signin-info">
                    <div class="logopanel m-b">
                        @include('flash::message')
                        
                    </div>
                    <div class="m-b"></div>
                    <h4>欢迎使用 <span class="label label-info">{{ config('app.name', 'Laravel') }}</span></h4>

                </div>
            </div>
            <div class="col-sm-7 animated fadeInRight">
                <form method="post" action="{{route('login-handle')}}">
                    {{csrf_field()}}
                    <p class="login-title">登录</p>
                    <p class="m-t-md" style="color:#666">登录到{{ config('app.name', 'Laravel') }}系统后台管理</p>
                    <input type="text" class="form-control uname" name="name" value="{{old('name')}}" required placeholder="用户名" />
                    <input type="password" class="form-control pword m-b" name="password" required placeholder="密码" />
                    <div style="width: 300px;">
                        {!! Geetest::render() !!}
                    </div>
                    <p></p>
{{--                    <div class="g-recaptcha" data-sitekey="6LdESp4kAAAAAGxfbeTbs4f3qJJJRa1USNB6B4MG"></div>--}}
{{--                    <div class="g-recaptcha" data-sitekey="6LebSJ4kAAAAAIOIQYoid24mG0lrbWmGmGMLrRJe"></div>--}}
                    <button class="btn btn-success btn-block">登录</button>
                    <p></p>
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <h4>有错误发生：</h4>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            </div>
        </div>
        <div class="signup-footer animated fadeInUp" style="width: 100%;text-align: center;position: absolute;left: 0;bottom: 100px;">
            © 2014-2022 PDF Technologies, Inc. All Rights Reserved.
        </div>
    </div>
</body>
</html>
