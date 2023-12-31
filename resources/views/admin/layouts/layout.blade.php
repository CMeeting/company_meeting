<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backend Administration Center - @yield('title', config('app.name', 'Laravel'))</title>
    <meta name="keywords" content="{{ config('app.name', 'Laravel') }}">
    <meta name="description" content="{{ config('app.name', 'Laravel') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="/favicon.ico">
    <link href="{{loadEdition('/admin/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/style.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{loadEdition('/admin/yanshi/jquery.nestable.css')}}">
    <link rel="stylesheet" href="{{loadEdition('/admin/yanshi/classify.css')}}">
    <link rel="stylesheet" href="{{loadEdition('/admin/yanshi/ladda-themeless.min.css')}}"/>
    <link rel="stylesheet" href="{{loadEdition('/admin/css/hljs.css')}}" />
    <link rel="stylesheet" href="{{loadEdition('/admin/css/index.min.css')}}" />
    <link rel="stylesheet" href="{{loadEdition('/admin/css/github-markdown.css')}}" />
    <script src="{{loadEdition('/admin/js/index.umd.js')}}"></script>
    <script src="{{loadEdition('/admin/js/plugin-gfm.umd.js')}}"></script>
    <script src="{{loadEdition('/admin/js/highlight.js')}}"></script>
    <script src="{{loadEdition('/admin/js/plugin-gemoji.umd.js')}}"></script>
    @yield('css')
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    @include('flash::message')
    @yield('content')
</div>
<script src="{{loadEdition('/js/jquery.min.js')}}"></script>
<script src="{{loadEdition('/admin/js/bootstrap.min.js')}}"></script>
@yield('js')
<script>
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
</script>
@yield('footer-js')
</body>
</html>
