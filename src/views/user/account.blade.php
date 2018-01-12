@extends('crud::layout')
@section('title', 'Account')
@section('header', 'Account')

@section('content')
    <div class="">
        <div class="">
            <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12">

                {!! Form::open(['url' => 'admin/account/edit']) !!}
                <div class="clearfix form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-sm-2 control-label nopadding">Name</label>
                    <div class="col-sm-10">
                        {{  Form::text('name', $value = old('name',  isset($user->name) ? $user->name : null),  ['class' => 'form-control'], $attributes = array()) }}
                    @if ($errors->has('name'))
                            <span class="help-block">
                                  <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="clearfix form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-sm-2 control-label nopadding">email</label>
                    <div class="col-sm-10">
                        {{  Form::email('email', $value = $user->email,  ['class' => 'form-control'], $attributes = array()) }}
                         @if ($errors->has('email'))
                            <span class="help-block">
                                  <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="clearfix form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-sm-2 control-label nopadding">password</label>
                    <div class="col-sm-10">
                        {{  Form::password('password', ['class' => 'form-control']) }}
                    @if ($errors->has('password'))
                            <span class="help-block">
                                  <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="clearfix form-group {{ $errors->has('password_confirm') ? ' has-error' : '' }}">
                    <label for="password_confirm" class="col-sm-2 control-label nopadding">password confirm</label>
                    <div class="col-sm-10">
                        {{  Form::password('password_confirm', ['class' => 'form-control']) }}
                        @if ($errors->has('password_confirm'))
                            <span class="help-block">
                                  <strong>{{ $errors->first('password_confirm') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
