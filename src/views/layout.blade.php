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

    <title>@yield('title') - Admin</title>

    <!-- Bootstrap core CSS -->
{!!Html::style('packages/wbe/crud/assets/bootstrap/bootstrap.min.css')!!}

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">-->

    <!-- Custom styles for this template -->
    {!!Html::style('packages/wbe/crud/assets/crud.css')!!}



    <script src="https://cdn.jsdelivr.net/jquery/3.1.1/jquery.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    {!! Html::script('packages/wbe/crud/assets/crud.js') !!}

    {!! Rapyd::styles() !!}
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">

        <div class="navbar-header">

            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" title="На головну" href="{{ url('admin/') }}">Admin</a>
        </div>


        <div style="height: 1px;" aria-expanded="false" id="navbar" class="navbar-collapse collapse">

            @include('crud::common.menu')

            <ul class="nav navbar-nav navbar-right">
                @if (!Auth::guard('admin')->check())
                    <li><a href="{{ url('admin/login/') }}">{{ trans('crud::common.login') }}</a></li>
                    <li><a href="{{ url('admin/register/') }}">{{ trans('crud::common.register') }}</a></li>
                @else
                    <li><a href="{{ url('admin/logout/') }}">{{ trans('crud::common.logout') }}</a></li>
                    <li><a href="{{ url('admin/account') }}">{{ trans('crud::common.hello') }}, {{ isset(Auth::guard('admin')->user()->name) ? Auth::guard('admin')->user()->name : Auth::guard('admin')->user()->email }}</a> </li>
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

<div class="container-fluid">
    <div class="row">
        {{--
         <li class="nav-divider"></li>

         @if (!isset($hide_sidebar))
        <div class="col-sm-3 col-md-2 sidebar">
                @include('backend.common.menu')
        </div>
        @endif--}}
        {{--<div class="@if (!isset($hide_sidebar)) col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 @endif main"> --}}
        <div class="col-lg-12 main">
            @yield('content')
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
