<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!--<link rel="icon" href="../../favicon.ico">-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Admin</title>
    <!-- Bootstrap core CSS -->
    {!!Html::style('packages/wbe/crud/assets/bootstrap/bootstrap.min.css')!!}
    {{--{!!Html::style('packages/wbe/crud/assets/admin_lte/libs/bootstrap/dist/css/bootstrap.min.css')!!}--}}
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">-->
    <!-- Custom styles for this template -->
    {{--{!!Html::style('packages/wbe/crud/assets/crud.css')!!}--}}
    {!!Html::style('packages/wbe/crud/assets/admin_lte/libs/font-awesome/css/font-awesome.min.css')!!}
    {!!Html::style('packages/wbe/crud/assets/admin_lte/libs/AdminLTE/dist/css/AdminLTE.min.css')!!}
    {!!Html::style('packages/wbe/crud/assets/admin_lte/libs/AdminLTE/dist/css/skins/skin-blue.css')!!}
    {!!Html::style('packages/wbe/crud/assets/admin_lte/libs/iCheck/skins/flat/blue.css')!!}
    {!!Html::style('packages/wbe/crud/assets/admin_lte/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')!!}
    {!!Html::style('packages/wbe/crud/assets/admin_lte/libs/datatables.net-bs/css/dataTables.bootstrap.min.css')!!}
    {!!Html::style('packages/wbe/crud/assets/admin_lte/css/main.css')!!}
    <script src="https://cdn.jsdelivr.net/jquery/3.1.1/jquery.min.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    {!! Html::script('packages/wbe/crud/assets/crud.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/jquery/dist/jquery.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/bootstrap/dist/js/bootstrap.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/AdminLTE/dist/js/app.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') !!}
		{{--{!! Html::script('packages/wbe/crud/assets/admin_lte/libs/ckeditor/ckeditor.js') !!}--}}
		{{--{!! Html::script('packages/wbe/crud/assets/admin_lte/libs/ckeditor/config.js') !!}--}}
	{!! Html::script('packages/zofe/rapyd/assets/ckeditor/ckeditor.js') !!}
	{!! Html::script('packages/zofe/rapyd/assets/ckeditor/config.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net/js/jquery.dataTables.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net-responsive/js/dataTables.responsive.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net-scroller/js/dataTables.scroller.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/js/main.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/iCheck/icheck.min.js') !!}

    {!! Rapyd::styles() !!}
</head>

<body class="skin-blue sidebar-mini">
{{--<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 main">
            @yield('content')
        </div>
    </div>
</div>--}}
<div class="wrapper">

    @if (Auth::guard('admin')->check())
    <header class="main-header clearfix">
        <a href="{{ url('admin/') }}" class="logo">
            <span class="logo-mini"><img class="img img-responsive" src="/packages/wbe/crud/assets/LaraSmart.png"></span>
            <span class="logo-lg"><img class="img img-responsive" src="/packages/wbe/crud/assets/LaraSmart.png"></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="container-fluid">
                <div style="height: 1px;" aria-expanded="false" id="navbar" class="navbar-collapse collapse">

                    @include('crud::common.menu')

                    <ul class="nav navbar-nav navbar-right">
                        @if (!Auth::guard('admin')->check())
                            <li><a href="{{ url('admin/login/') }}">{{ trans('crud::common.login') }}</a></li>
                            {{--<li><a href="{{ url('admin/register/') }}">{{ trans('crud::common.register') }}</a></li>--}}
                        @else
                            <li><a href="{{ url('admin/logout/') }}">{{ trans('crud::common.logout') }}</a></li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#">{{ trans('crud::common.hello') }}, {{ isset(Auth::guard('admin')->user()->name) ? Auth::guard('admin')->user()->name : Auth::guard('admin')->user()->email }}<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a  href="{{ url('admin/account') }}">Account</a></li>
                                    <li><a  href="{{ url('admin/account/settings') }}">Settings</a></li>
                                </ul>
                            </li>
                        @endif

                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                {{ trans('crud::common.languages') }}
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @if(isset($languages))
                                    @foreach($languages as $language)
                                        <li>
                                            <a href="{{ url('admin/setlocale/' . $language->code) }}">{{ $language->name }}</a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <acide class="main-sidebar">
        <section class="sidebar">
            @include('crud::common.vertical_menu')
        </section>
    </acide>
    @endif
    <div class="content-wrapper">
        <div class="box box-info box-info_tables">

            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                        <div class="alert alert-{{ $msg }}">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ Session::pull('alert-' . $msg) }}
                        </div>
                    @endif
                @endforeach
            </div>
            @if (Auth::guard('admin')->check())
            <div class="box-header">
                <h3 class="box-title">@yield('title')</h3>
            </div>
            @endif
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
{!! Html::script('packages/wbe/crud/assets/bootstrap/bootstrap.min.js') !!}
{{--!! Html::script('/js/datepicker/bootstrap-datepicker.js') !!--}}

@section('scripts')


@show
{!! Rapyd::scripts() !!}

</body>
</html>
