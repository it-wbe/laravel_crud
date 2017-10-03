{{--@extends('crud::layout')
@section('title', 'Login')
@section('header', 'Login')

@section('content')--}}
{{--{!!Html::style('packages/wbe/crud/assets/admin_lte/libs/AdminLTE/dist/css/skins/skin-green-light.min.css')!!}--}}
    
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

    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/ckeditor/ckeditor.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/ckeditor/config.js') !!}



    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net/js/jquery.dataTables.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net-responsive/js/dataTables.responsive.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/datatables.net-scroller/js/dataTables.scroller.min.js') !!}
    {!! Html::script('packages/wbe/crud/assets/admin_lte/js/main.js') !!}

    {!! Html::script('packages/wbe/crud/assets/admin_lte/libs/iCheck/icheck.min.js') !!}

    {!! Rapyd::styles() !!}
</head>
<body class="skin-blue sidebar-mini">
            {{--<div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('admin/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>--}}

	<div class="wrapper">
	   <div class="box box-info box-info_main-page">
			<div class="box-header with-border">
				<h3 class="box-title">Login</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" role="form" method="POST" action="{{ url('admin/login') }}">
				{{ csrf_field() }}

				<div class="box-body">
					<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
						<label for="inputEmail3" class="col-sm-2 control-label">Email</label>

						<div class="col-sm-10">
							<input type="email" class="form-control" name="email" id="inputEmail3" value="{{ old('email') }}" required placeholder="Email">
							@if ($errors->has('email'))
								<span class="help-block">
									<strong>{{ $errors->first('email') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
						<label for="inputPassword3" class="col-sm-2 control-label">Password</label>

						<div class="col-sm-10">
							<input type="password" required name="password" class="form-control" id="inputPassword3" placeholder="Password">

							@if ($errors->has('password'))
								<span class="help-block">
									<strong>{{ $errors->first('password') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-1 col-sm-5 col-md-6 col-md-offset-0">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="remember"> Remember me
								</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-6 col-md-offset-0">
						   <button type="submit" class="btn btn-info pull-right">Sign in</button>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
				<div class="box-footer">
					<a href="{{route('password.reset')}}" class="">Forget Password?</a>
				</div>
				<!-- /.box-footer -->
			</form>
		</div>
    </div>
</body>
{{--@endsection--}}

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
{!! Html::script('packages/wbe/crud/assets/bootstrap/bootstrap.min.js') !!}
{{--!! Html::script('/js/datepicker/bootstrap-datepicker.js') !!--}}
</html>